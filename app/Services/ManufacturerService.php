<?php
namespace App\Services;

class ManufacturerService
{
    public static function normalize(string $name): string
    {
        $name = strtolower(trim($name));

        // remove punctuation
        $name = preg_replace('/[^\w\s]/', '', $name);

        // remove common legal suffixes
        $name = preg_replace('/\b(inc|ltd|llc|gmbh|corp|co)\b/', '', $name);

        // collapse whitespace
        $name = preg_replace('/\s+/', ' ', $name);

        return trim($name);
    }

    public static function resolveByName(string $name, int $userId): int
    {
        $normalized = self::normalize($name);

        // 1) check alias table
        $alias = \App\Models\ManufacturerAlias::where(
            'manufacturer_alias_owner_u_fk',
            $userId
        )->where(
                'alias_normalized',
                $normalized
            )->first();

        if ($alias) {
            return $alias->manufacturer_id_fk;
        }

        // 2) check canonical manufacturers directly
        $manufacturer = \App\Models\Manufacturer::where(
            'manufacturer_owner_u_fk',
            $userId
        )->whereRaw('LOWER(manufacturer_name) = ?', [strtolower($name)])
            ->first();

        if ($manufacturer) {
            // create alias automatically
            \App\Models\ManufacturerAlias::create([
                'manufacturer_id_fk' => $manufacturer->manufacturer_id,
                'manufacturer_alias_owner_u_fk' => $userId,
                'alias' => $name,
                'alias_normalized' => $normalized,
            ]);

            return $manufacturer->manufacturer_id;
        }

        // 3) create manufacturer + alias
        $manufacturer = \App\Models\Manufacturer::create([
            'manufacturer_name' => $name,
            'manufacturer_owner_u_fk' => $userId,
        ]);

        \App\Models\ManufacturerAlias::create([
            'manufacturer_id_fk' => $manufacturer->manufacturer_id,
            'manufacturer_alias_owner_u_fk' => $userId,
            'alias' => $name,
            'alias_normalized' => $normalized,
        ]);

        return $manufacturer->manufacturer_id;
    }
}