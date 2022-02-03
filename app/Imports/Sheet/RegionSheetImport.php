<?php

namespace App\Imports\Sheet;

use App\Models\Region;
use Maatwebsite\Excel\Concerns\ToModel;

class RegionSheetImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new Region([
            "kode_region" => $row[0],
            "nama_region" => $row[1],
        ]);
    }
}
