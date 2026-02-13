<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackfillBomRevisions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bom:backfill-bom-revisions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill BOM revisions using original BOM created_at as revision timestamp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $boms = \App\Models\Bom::all();

        foreach ($boms as $bom) {
            if ($bom->current_revision_id) {
                continue; // already backfilled
            }

            \DB::transaction(function () use ($bom) {
                $rev = \DB::table('bom_revisions')->insertGetId([
                    'bom_id_fk' => $bom->bom_id,
                    'revision_number' => 1,
                    'label' => null,
                    'note' => null,
                    'created_by_u_fk' => $bom->bom_owner_u_fk ?? null,
                    'created_at' => $bom->created_at,   // key requirement
                    'updated_at' => $bom->created_at,
                ], 'bom_revision_id');

                $elements = \App\Models\BomElements::where('bom_id_fk', $bom->bom_id)->get();

                foreach ($elements as $el) {
                    \DB::table('bom_revision_elements')->insert([
                        'bom_revision_id_fk' => $rev,
                        'part_id_fk' => $el->part_id_fk,
                        'quantity' => (int) $el->element_quantity,
                        'refdes' => null,
                        'line_uuid' => null,
                        'created_at' => $bom->created_at,
                        'updated_at' => $bom->created_at,
                    ]);
                }

                $bom->current_revision_id = $rev;
                $bom->save();
            });
        }

        // This is for already existing BOM revisions that didn't get a revision number assigned
        // if the migration was run after some revisions were created)

        $bomIds = \DB::table('bom_revisions')
            ->select('bom_id_fk')
            ->distinct()
            ->pluck('bom_id_fk');

        foreach ($bomIds as $bomId) {
            \DB::transaction(function () use ($bomId) {
                $revs = \DB::table('bom_revisions')
                    ->where('bom_id_fk', $bomId)
                    ->orderBy('created_at')
                    ->orderBy('bom_revision_id')
                    ->lockForUpdate()
                    ->get(['bom_revision_id']);

                $i = 1;
                foreach ($revs as $rev) {
                    \DB::table('bom_revisions')
                        ->where('bom_revision_id', $rev->bom_revision_id)
                        ->update(['revision_number' => $i]);
                    $i++;
                }
            });
        }

        $this->info('Backfill complete.');
    }

}
