<?php

namespace App\Imports;

use App\Imports\Sheet\SheetImport;
use App\Models\Alasan;
use App\Models\Area;
use App\Models\Brand;
use App\Models\BudgetHolder;
use App\Models\Category;
use App\Models\Distributor;
use App\Models\DistributorGroup;
use App\Models\Divisi;
use App\Models\DocumentClaim;
use App\Models\Investment;
use App\Models\Product;
use App\Models\Region;
use App\Models\Spend;
use App\Models\SubBrand;
use App\Models\Tax;
use App\Models\TipePromo;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MasterDataImport implements WithMultipleSheets
{
  use WithConditionalSheets;

  public function conditionalSheets(): array
  {
    return [
      Alasan::WORKSHEET_NAME => new SheetImport(),
      Area::WORKSHEET_NAME => new SheetImport(),
      Brand::WORKSHEET_NAME => new SheetImport(),
      BudgetHolder::WORKSHEET_NAME => new SheetImport(),
      Category::WORKSHEET_NAME => new SheetImport(),
      Distributor::WORKSHEET_NAME => new SheetImport(),
      DistributorGroup::WORKSHEET_NAME => new SheetImport(),
      Divisi::WORKSHEET_NAME => new SheetImport(),
      DocumentClaim::WORKSHEET_NAME => new SheetImport(),
      Investment::WORKSHEET_NAME => new SheetImport(),
      Product::WORKSHEET_NAME => new SheetImport(),
      Region::WORKSHEET_NAME => new SheetImport(),
      Spend::WORKSHEET_NAME => new SheetImport(),
      SubBrand::WORKSHEET_NAME => new SheetImport(),
      Tax::WORKSHEET_NAME => new SheetImport(),
      TipePromo::WORKSHEET_NAME => new SheetImport(),
    ];
  }
}
