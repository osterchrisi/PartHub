<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;

    protected $table = 'user_settings';

    protected $primaryKey = 'user_settings_id';

    protected $fillable = ['user_id_fk', 'setting_name', 'setting_value'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_fk');
    }
}
