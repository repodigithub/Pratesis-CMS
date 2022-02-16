<?php

namespace App\Http\Controllers;

use App\Models\Alasan;
use App\Models\Area;
use App\Models\Brand;
use App\Models\BudgetHolder;
use App\Models\Category;
use App\Models\Distributor;
use App\Models\DistributorGroup;
use App\Models\Divisi;
use App\Models\DocumentClaim;
use App\Models\File;
use App\Models\Investment;
use App\Models\Product;
use App\Models\Region;
use App\Models\Spend;
use App\Models\SubBrand;
use App\Models\Tax;
use App\Models\TipePromo;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function index(Request $req)
  {
    if ($req->filled('search')) {
      return $this->indexType("", $req);
    }

    $models = [
      Alasan::FILE_PATH,
      Area::FILE_PATH,
      Brand::FILE_PATH,
      BudgetHolder::FILE_PATH,
      Category::FILE_PATH,
      Distributor::FILE_PATH,
      DistributorGroup::FILE_PATH,
      Divisi::FILE_PATH,
      DocumentClaim::FILE_PATH,
      Investment::FILE_PATH,
      Product::FILE_PATH,
      Region::FILE_PATH,
      Spend::FILE_PATH,
      SubBrand::FILE_PATH,
      Tax::FILE_PATH,
      TipePromo::FILE_PATH,
    ];

    return $this->response(["type" => $models]);
  }

  public function indexType($type, Request $req)
  {
    $pagination = $this->getPagination($req, ["public_path", "asc"]);

    $data = File::select("*")->where("public_path", "ILIKE", "%/{$type}/%");

    if ($req->filled('search')) {
      $data->where("public_path", "ILIKE", "%{$req->query("search")}%");
    }

    if (!empty($pagination->sort)) {
      $sort = $pagination->sort;
      $data->orderBy($sort[0], $sort[1]);
    }

    $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

    return $this->response($data);
  }
}
