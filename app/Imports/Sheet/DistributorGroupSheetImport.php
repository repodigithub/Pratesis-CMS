<?php

namespace App\Imports\Sheet;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class DistributorGroupSheetImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
    }
}
