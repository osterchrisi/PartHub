<?php 

namespace App\Services\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class CsvCollectionImporter implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        return $collection;
    }
}
