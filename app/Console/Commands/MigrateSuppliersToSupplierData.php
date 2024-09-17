<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Part;
use App\Models\SupplierData;

class MigrateSuppliersToSupplierData extends Command
{
    protected $signature = 'migrate:suppliers-to-data {--dry-run : Simulate the migration without modifying the database}';
    protected $description = 'Migrate part suppliers from part_supplier_fk column to the supplier_data table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Check if it's a dry run
        $isDryRun = $this->option('dry-run');

        Part::whereNotNull('part_supplier_fk')
            ->chunk(100, function ($parts) use ($isDryRun) {
                foreach ($parts as $part) {
                    $exists = SupplierData::where('part_id_fk', $part->part_id)
                        ->where('supplier_id_fk', $part->part_supplier_fk)
                        ->exists();

                    if (!$exists) {
                        if ($isDryRun) {
                            // Simulate the migration by logging the changes
                            $this->info("Simulating: Insert into supplier_data for part_id: {$part->part_id}, supplier_id: {$part->part_supplier_fk}");
                        }
                        else {
                            // Perform the actual migration
                            SupplierData::create([
                                'part_id_fk' => $part->part_id,
                                'supplier_id_fk' => $part->part_supplier_fk,
                                'URL' => null,
                                'SPN' => null,
                                'price' => null,
                                'supplier_data_owner_u_fk' => $part->part_owner_u_fk,
                                'supplier_data_owner_g_fk' => null,
                            ]);
                        }
                    }
                }
            });

        if ($isDryRun) {
            $this->info('Dry run complete. No data was changed.');
        }
        else {
            $this->info('Migration complete.');
        }
    }
}
