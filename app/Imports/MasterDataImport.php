<?php

namespace App\Imports;

use App\Imports\Sheet\SheetImport;
use App\Models\Area;
use App\Models\Distributor;
use App\Models\DistributorGroup;
use App\Models\Region;
use App\Models\SubBrand;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MasterDataImport implements WithMultipleSheets
{
  use WithConditionalSheets;

  public function conditionalSheets(): array
  {
    return [
      Area::WORKSHEET_NAME => new SheetImport(),
      Distributor::WORKSHEET_NAME => new SheetImport(),
      DistributorGroup::WORKSHEET_NAME => new SheetImport(),
      Region::WORKSHEET_NAME => new SheetImport(),
      SubBrand::WORKSHEET_NAME => new SheetImport(),
    ];
  }
}
