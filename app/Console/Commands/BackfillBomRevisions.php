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
    protected $signature = 'app:backfill-bom-revisions';

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

    $this->info('Backfill complete.');
}

}
