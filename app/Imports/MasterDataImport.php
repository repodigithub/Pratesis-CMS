<?php

namespace App\Imports;

use App\Imports\Sheet\SheetImport;
use App\Models\Area;
use App\Models\DistributorGroup;
use App\Models\Region;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MasterDataImport implements WithMultipleSheets
{
  use WithConditionalSheets;

  public function conditionalSheets(): array
  {
    return [
      Region::WORKSHEET_NAME => new SheetImport(),
      DistributorGroup::WORKSHEET_NAME => new SheetImport(),
      Area::WORKSHEET_NAME => new SheetImport(),
    ];
  }
}
