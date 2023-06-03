<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomRun extends Model
{
    use HasFactory;
    protected $table = 'bom_runs';
    protected $primaryKey = 'bom_run_id';
    public $timestamps = false;

    public function bom()
    {
        return $this->belongsTo(BOM::class, 'bom_id_fk');
    }
}
