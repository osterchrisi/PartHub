<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\ManufacturerAlias;

class Manufacturer extends Model
{
    use HasFactory;

    protected $table = 'manufacturers';
    protected $primaryKey = 'manufacturer_id';

    protected $fillable = [
        'manufacturer_name',
        'manufacturer_contact_name',
        'manufacturer_email',
        'manufacturer_phone',
        'manufacturer_website',
        'manufacturer_comment',
        'manufacturer_owner_u_fk',
        'manufacturer_owner_g_fk',
    ];

    public function aliases()
    {
        return $this->hasMany(ManufacturerAlias::class, 'manufacturer_id_fk', 'manufacturer_id');
    }


    public static function availableManufacturers($format = 'json')
    {
        $user = Auth::user();

        $manufacturers = self::where('manufacturer_owner_u_fk', $user->id)->get();

        if ($format === 'json')
            return $manufacturers->toJson();
        if ($format === 'array')
            return $manufacturers->toArray();

        return $manufacturers;
    }

    public static function getManufacturerById($manufacturer_id)
    {
        return self::find($manufacturer_id);
    }

    public static function createManufacturer(array $data)
    {
        $user_id = Auth::user()->id;

        $m = new self;
        $m->manufacturer_name = $data['manufacturer_name'];
        $m->manufacturer_contact_name = $data['manufacturer_contact_name'] ?? null;
        $m->manufacturer_email = $data['manufacturer_email'] ?? null;
        $m->manufacturer_phone = $data['manufacturer_phone'] ?? null;
        $m->manufacturer_website = $data['manufacturer_website'] ?? null;
        $m->manufacturer_comment = $data['manufacturer_comment'] ?? null;
        $m->manufacturer_owner_u_fk = $user_id;
        $m->manufacturer_owner_g_fk = null;
        $m->save();

        return $m->manufacturer_id;
    }
}
