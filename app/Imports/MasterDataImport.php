<?php

namespace App\Imports;

use App\Imports\Sheet\RegionSheetImport;
use App\Models\Region;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MasterDataImport implements WithMultipleSheets
{
  use WithConditionalSheets;

  public function conditionalSheets(): array
  {
    return [
      Region::WORKSHEET_NAME => new RegionSheetImport(),
    ];
  }
}
