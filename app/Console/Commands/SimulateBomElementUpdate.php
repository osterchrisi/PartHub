<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SimulateBomElementUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simulate:bom-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulates updating bom_elements owner column';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $results = DB::table('bom_elements')
            ->join('boms', 'bom_elements.bom_id_fk', '=', 'boms.bom_id')
            ->select('bom_elements.bom_elements_id as bom_element_id', 'bom_elements.bom_id_fk', 'boms.bom_owner_u_fk as potential_owner')
            ->get();

        $this->info('Simulation Results:');
        $this->line(json_encode($results, JSON_PRETTY_PRINT));


        return 0;
    }
}
