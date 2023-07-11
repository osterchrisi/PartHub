<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BomRun extends Model
{
    use HasFactory;
    protected $table = 'bom_runs';
    protected $primaryKey = 'bom_run_id';
    public $timestamps = false;

    protected $fillable = [
        'bom_id_fk',
        'bom_run_quantity'
    ];

    public function bom()
    {
        return $this->belongsTo(BOM::class, 'bom_id_fk');
    }
    
    public static function createBomRun($bom_id, $quantity, $user_id){
        $bom_run = new BomRun([
            'bom_id_fk' => $bom_id,
            'bom_run_quantity' => $quantity,
        ]);
        $bom_run->bom_run_user_fk = auth()->user()->id;
        $bom_run->save();

        $bom_run_id = $bom_run->bom_run_id;

        return $bom_run_id;
    }

    public static function getBomRunsByBomId($bom_id)
    {
        $bom_runs = DB::table('bom_runs')
            ->join('boms', 'bom_id_fk', '=', 'boms.bom_id')
            ->join('users', 'bom_run_user_fk', '=', 'id')
            ->select('bom_run_datetime', 'bom_run_quantity', 'name')
            ->where('bom_id_fk', $bom_id)
            ->get()
            ->toArray();

        return $bom_runs;
    }
}
