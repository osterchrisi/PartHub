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

    protected $fillable = [
        'bom_id_fk',
        'bom_run_quantity',
    ];

    public function bom()
    {
        return $this->belongsTo(BOM::class, 'bom_id_fk');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'bom_run_user_fk');
    }

    public static function createBomRun($bom_id, $quantity, $user_id)
    {
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
        return self::with('user') // Eager load the user relationship
            ->where('bom_id_fk', $bom_id)
            ->get();
    }
}
