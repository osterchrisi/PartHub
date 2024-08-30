<?php

namespace App\Services\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CsvCollectionImporter implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        return $collection;
    }
}
