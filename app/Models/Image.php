<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filename',
        'image_owner_u_id',
        'type',
        'associated_id',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'document_owner_u_id');
    }
}
