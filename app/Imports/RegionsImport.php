<?php

namespace App\Imports;

use App\Models\Region;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class RegionsImport implements ToModel, WithHeadingRow
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
