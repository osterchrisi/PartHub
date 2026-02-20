<?php

namespace App\Console\Commands;

use App\Models\BomRevision;
use App\Models\BomRun;
use Illuminate\Console\Command;

class BackfillBomRunRevisionIds extends Command
{
    protected $signature = 'bom:backfill-bom-run-revision-ids {--dry-run}';

    protected $description = 'Backfill bom_runs.bom_revision_id_fk (NULL) to revision_number=1 per BOM';

    public function handle(): int
    {
        $runs = BomRun::query()
            ->whereNull('bom_revision_id_fk')
            ->select(['bom_run_id', 'bom_id_fk'])
            ->get()
            ->groupBy('bom_id_fk');

        $total = $runs->flatten(1)->count();

        if ($total === 0) {
            $this->info('Nothing to backfill.');

            return self::SUCCESS;
        }

        $this->info(($this->option('dry-run') ? 'Dry run: ' : '')."Found {$total} legacy bom_runs rows.");

        // Preload rev1 ids for all involved BOMs
        $bomIds = $runs->keys()->all();

        $rev1ByBom = BomRevision::query()
            ->whereIn('bom_id_fk', $bomIds)
            ->where('revision_number', 1)
            ->get(['bom_id_fk', 'bom_revision_id'])
            ->keyBy('bom_id_fk');

        $updated = 0;
        $missing = 0;

        foreach ($runs as $bomId => $bomRuns) {
            $rev1 = $rev1ByBom->get($bomId);

            if (! $rev1) {
                $missing += $bomRuns->count();

                continue;
            }

            if ($this->option('dry-run')) {
                $updated += $bomRuns->count();

                continue;
            }

            $runIds = $bomRuns->pluck('bom_run_id')->all();

            // Chunk updates to avoid huge IN() lists
            foreach (array_chunk($runIds, 500) as $chunk) {
                BomRun::query()
                    ->whereIn('bom_run_id', $chunk)
                    ->whereNull('bom_revision_id_fk')
                    ->update(['bom_revision_id_fk' => $rev1->bom_revision_id]);
                $updated += count($chunk);
            }
        }

        if ($this->option('dry-run')) {
            $this->info("Would update {$updated} rows. Missing Rev 1 for {$missing} rows.");

            return self::SUCCESS;
        }

        $this->info("Backfill complete: updated {$updated} rows. Missing Rev 1 for {$missing} rows.");

        return self::SUCCESS;
    }
}
