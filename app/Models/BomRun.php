<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * BomRun
 *
 * Records a build/assembly event of a BOM.
 * References the exact BOM revision that was used for the build.
 *
 * This is an append-only log.
 */
class BomRun extends Model
{
    use HasFactory;

    protected $table = 'bom_runs';

    protected $primaryKey = 'bom_run_id';

    protected $fillable = [
        'bom_id_fk',
        'bom_run_quantity',
        'bom_run_user_fk',
        'bom_revision_id_fk',
    ];


    public function bom()
    {
        return $this->belongsTo(BOM::class, 'bom_id_fk');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'bom_run_user_fk');
    }

    public static function createBomRun($bom_id, $quantity, $user_id, $bomRevisionId = null)
    {
        $bom_run = new BomRun([
            'bom_id_fk' => $bom_id,
            'bom_revision_id_fk' => $bomRevisionId,
            'bom_run_quantity' => $quantity,
        ]);

        // who built it
        $bom_run->bom_run_user_fk = $user_id;

        $bom_run->save();

        return $bom_run->bom_run_id;
    }


    public static function getBomRunsByBomId($bom_id)
    {
        return self::with('user') // Eager load the user relationship
            ->where('bom_id_fk', $bom_id)
            ->orderByDesc('bom_run_datetime')
            ->get();
    }
}
