<?php

namespace App\Http\Controllers;

use App\Imports\BomImport;
use App\Models\Bom;
use App\Models\BomElements;
use App\Models\BomRevision;
use App\Models\BomRevisionElement;
use App\Models\BomRun;
use App\Models\Part;
use App\Services\CsvImportService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BomController extends Controller
{
    private static $table_name = 'boms';

    private static $id_field = 'bom_id';

    private static $bom_list_table_headers = ['state', 'bom_name', 'bom_description', 'bom_id'];

    private static $nice_bom_list_table_headers = ['BOM Name', 'Description', 'ID'];

    private static $bom_detail_table_headers = ['part_name', 'quantity', 'stock_available', 'can_build'];

    private static $nice_bom_detail_table_headers = ['Part Name', 'Quantity needed', 'Total stock available', 'Can build'];

    private static $bomRunsTableHeaders = ['bom_run_datetime', 'bom_run_quantity', 'bom_revision_id_fk', 'name'];

    private static $nice_bomRunsTableHeaders = ['Build Time', 'Build Quantity', 'Revision', 'User'];

    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * The big table containing all BOMs
     */
    public static function index(Request $request)
    {
        $search_term = request()->has('search') ? request()->input('search') : '';
        $bom_list = Bom::searchBoms($search_term);

        // Return full BOM view or only BOM table depending on route
        $route = $request->route()->getName();
        if ($route == 'boms') {
            return view('boms.boms', [
                'title' => 'BOMs',
                'view' => 'boms',
                'bom_list' => $bom_list,
                'db_columns' => self::$bom_list_table_headers,
                'nice_columns' => self::$nice_bom_list_table_headers,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        }
        elseif ($route == 'boms.bomsTable') {
            return view('boms.bomsTable', [
                'bom_list' => $bom_list,
                'db_columns' => self::$bom_list_table_headers,
                'nice_columns' => self::$nice_bom_list_table_headers,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        }
    }

    /**
     * Detail view of a single BOM in the info window
     * @param mixed $bom_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public static function show($bom_id)
    {
        $bom = Bom::find($bom_id);

        if (!$bom) {
            abort(404, 'BOM not found.'); // Likely a deleted BOM. Could be handled in frontend: Just select next-best BOM after deletion.
        }

        $bom_name = $bom->bom_name;
        $bom_description = $bom->bom_description;
        $bom_owner = $bom->bom_owner_u_fk;

        if (Auth::user()->id !== $bom_owner) {
            abort(403, 'Unauthorized access.');
        }

        // $bom_elements = BomElements::getBomElements($bom_id);
        // Prefer revisions
        $bom->load('currentRevision');

        if (!$bom->currentRevision) {
            // Safety fallback (in case some BOMs didn't backfill correctly)
            $bom_elements = BomElements::getBomElements($bom_id);
        }
        else {
            $bom_elements = $bom->currentRevision
                ->elements()
                ->with('part')
                ->get();
        }

        $bom_runs = BomRun::getBomRunsByBomId($bom_id);

        return view(
            'boms.showBom',
            [
                'bom_id' => $bom_id,
                'bom_name' => $bom_name,
                'bom_description' => $bom_description,
                'bom_elements' => $bom_elements,

                // Bom Details Table
                'db_columns' => self::$bom_detail_table_headers,
                'nice_columns' => self::$nice_bom_detail_table_headers,

                // Bom Runs Table
                'nice_bomRunsTableHeaders' => self::$nice_bomRunsTableHeaders,
                'bomRunsTableHeaders' => self::$bomRunsTableHeaders,
                'bom_runs' => $bom_runs,

                // Tabs Settings
                'tabId1' => 'info',
                'tabText1' => 'Info',
                'tabToggleId1' => 'bomInfo',
                'tabId2' => 'history',
                'tabText2' => 'Build History',
                'tabToggleId2' => 'bomHistory',
            ]
        );
    }

    /**
     * Takes BOM(s), retrieves the BOM Elements (parts) and creates an array of changes to be requested
     *
     * @return mixed
     */
    public function prepareBomForAssembly(Request $request)
    {
        // Get the input values from the request
        $ids = $request->input('ids');

        $assemble_quantity = (int) $request->input('assemble_quantity');
        if ($assemble_quantity < 1)
            abort(422, 'Invalid assemble quantity.');

        $from_location = $request->input('from_location');

        // The array for all acquired stock changes to go in
        $all_stock_changes = [];

        // Iterate over each individual BOM
        foreach ($ids as $bom_id) {

            // Check if the user is authorized to access this BOM
            $bom = Bom::with(['currentRevision.elements'])->find($bom_id);

            if (!$bom) {
                abort(404, 'BOM not found.');
            }

            if (Auth::id() !== $bom->bom_owner_u_fk) {
                abort(403, 'Unauthorized access.');
            }

            // Retrieve BOM elements (parts)
            // $bom_elements = BomElements::getBomElements($bom_id);
            $revision = $bom->currentRevision;
            if (!$revision)
                abort(500, 'BOM has no current revision.');
            $revisionId = $revision->bom_revision_id;

            // Iterating over BOM elements (parts)
            foreach ($revision->elements as $element) {
                $element_quantity = (int) $element->quantity;
                $part_id = (int) $element->part_id_fk;
                $reducing_quantity = $assemble_quantity * $element_quantity;

                $all_stock_changes[] = [
                    'bom_id' => $bom_id,
                    'bom_revision_id' => $revisionId,
                    'part_id' => $part_id,
                    'change' => -1,
                    'quantity' => $reducing_quantity,
                    'to_location' => null,
                    'from_location' => $from_location,
                    'comment' => 'BOM build of BOM with ID ' . $bom_id,
                    'status' => 'BOM build request',
                    'assemble_quantity' => $assemble_quantity,
                ];
            }
        }

        return $this->stockService->handleStockRequest($all_stock_changes);
    }

    public function importBom(Request $request)
    {
        // Form data
        $file = $request->file('bom_file');
        $bom_name = $request->input('bom_name');
        $bom_description = $request->input('bom_description');

        if (!$file) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        try {
            // Begin SQL transaction
            DB::beginTransaction();

            // Create new BOM
            $bom_id = Bom::createBom($bom_name, $bom_description);

            // Instantiate the CsvImportService
            $csvImportService = new CsvImportService;

            // Process the uploaded file using BomImport
            Excel::import(new BomImport($bom_id, $csvImportService), $file);

            // Persist database changes and set success flash message
            DB::commit();

            return response()->json(['success' => 'BOM "' . $bom_name . '" imported successfully.', 'new_bom_id' => $bom_id]);

        } catch (\Exception $e) {
            // Roll back database changes made so far
            DB::rollback();

            // Set error flash message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function commit(Request $request, $id)
    {
        $bom = Bom::with('currentRevision')->find($id);
        if (!$bom)
            abort(404, 'BOM not found.');
        if (Auth::id() !== $bom->bom_owner_u_fk)
            abort(403, 'Unauthorized access.');

        $data = $request->validate([
            'base_revision_id' => ['required', 'integer'],
            'lines' => ['required', 'array'],
            'lines.*.part_id' => ['required', 'integer'],
            'lines.*.quantity' => ['required', 'integer', 'min:1'],
            'lines.*.refdes' => ['nullable', 'string', 'max:1000'],
            'lines.*.line_uuid' => ['nullable', 'string', 'max:64'],
        ]);

        $currentRevisionId = (int) ($bom->current_revision_id ?? 0);
        $baseRevisionId = (int) $data['base_revision_id'];

        // Strict mode: editor must be based on current revision
        if ($currentRevisionId !== $baseRevisionId) {
            return response()->json([
                'error' => 'BOM changed since you opened the editor. Reload and try again.'
            ], 409);
        }

        // Merge duplicates server-side (by part_id)
        $merged = [];
        foreach ($data['lines'] as $line) {
            $pid = (int) $line['part_id'];
            $qty = (int) $line['quantity'];
            $ref = trim((string) ($line['refdes'] ?? ''));
            $uuid = $line['line_uuid'] ?? null;

            if (!isset($merged[$pid])) {
                $merged[$pid] = [
                    'part_id_fk' => $pid,
                    'quantity' => 0,
                    'refdes' => '',
                    'line_uuid' => $uuid,
                ];
            }

            $merged[$pid]['quantity'] += $qty;

            if ($ref !== '') {
                $merged[$pid]['refdes'] = $merged[$pid]['refdes'] !== ''
                    ? ($merged[$pid]['refdes'] . ',' . $ref)
                    : $ref;
            }

            // keep first non-null uuid if any
            if (!$merged[$pid]['line_uuid'] && $uuid) {
                $merged[$pid]['line_uuid'] = $uuid;
            }
        }

        // Ownership check for parts
        $partIds = array_keys($merged);
        $ownedCount = Part::whereIn('part_id', $partIds)
            ->where('part_owner_u_fk', Auth::id())
            ->count();

        if ($ownedCount !== count($partIds)) {
            return response()->json(['error' => 'One or more parts are not owned by the current user.'], 422);
        }

        $newRevisionId = null;

        DB::transaction(function () use ($bom, $merged, &$newRevisionId) {
            $rev = BomRevision::create([
                'bom_id_fk' => $bom->bom_id,
                'label' => null,
                'note' => null,
                'created_by_u_fk' => Auth::id(),
            ]);

            $newRevisionId = $rev->bom_revision_id;

            foreach ($merged as $row) {
                BomRevisionElement::create([
                    'bom_revision_id_fk' => $newRevisionId,
                    'part_id_fk' => $row['part_id_fk'],
                    'quantity' => $row['quantity'],
                    'refdes' => $row['refdes'] !== '' ? $row['refdes'] : null,
                    'line_uuid' => $row['line_uuid'],
                ]);
            }

            $bom->current_revision_id = $newRevisionId;
            $bom->save();
        });

        return response()->json([
            'status' => 'success',
            'current_revision_id' => $newRevisionId,
        ]);
    }


    public function editor($id)
    {
        $bom = Bom::with(['currentRevision'])->find($id);
        if (!$bom)
            abort(404, 'BOM not found.');
        if (Auth::id() !== $bom->bom_owner_u_fk)
            abort(403, 'Unauthorized access.');

        $rev = $bom->currentRevision;

        if (!$rev) {
            // fallback (should be rare after backfill)
            // TODO: remove after bom_elements deprecation
            $elements = BomElements::with('part')->where('bom_id_fk', $id)->get();

            $lines = $elements->map(fn($e) => [
                'part_id' => (int) $e->part_id_fk,
                'part_name' => $e->part?->part_name ?? '',
                'quantity' => (int) $e->element_quantity,
                'refdes' => null,
                'line_uuid' => null,
            ])->values();

            return response()->json([
                'bom' => [
                    'bom_id' => (int) $bom->bom_id,
                    'bom_name' => $bom->bom_name,
                    'bom_description' => $bom->bom_description,
                ],
                'revision' => [
                    'bom_revision_id' => null,
                    'label' => null,
                    'created_at' => null,
                ],
                'lines' => $lines,
            ]);
        }

        $rev->load(['elements.part']);

        $lines = $rev->elements->map(fn($e) => [
            'part_id' => (int) $e->part_id_fk,
            'part_name' => $e->part?->part_name ?? '',
            'quantity' => (int) $e->quantity,
            'refdes' => $e->refdes,
            'line_uuid' => $e->line_uuid,
        ])->values();

        return response()->json([
            'bom' => [
                'bom_id' => (int) $bom->bom_id,
                'bom_name' => $bom->bom_name,
                'bom_description' => $bom->bom_description,
            ],
            'revision' => [
                'bom_revision_id' => (int) $rev->bom_revision_id,
                'label' => $rev->label,
                'created_at' => optional($rev->created_at)->toIso8601String(),
            ],
            'lines' => $lines,
        ]);
    }

}
