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

    $data = File::with('uploader:id,full_name')->select("*")->where("public_path", "ILIKE", "%/{$type}/%");

    if ($req->filled('search')) {
      $data->where(function ($q) use ($req) {
        $q->where("public_path", "ILIKE", "%{$req->query("search")}%");
        $q->orWhere("title", "ILIKE", "%{$req->query("search")}%");
      });
    }

    $query = clone $data;

    if (!empty($pagination->sort)) {
      $sort = $pagination->sort;
      $data->orderBy($sort[0], $sort[1]);
    }

    $data_page = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);
    $data_group = [
      Alasan::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . Alasan::FILE_PATH . "/%")->count(),
      Area::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . Area::FILE_PATH . "/%")->count(),
      Brand::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . Brand::FILE_PATH . "/%")->count(),
      BudgetHolder::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . BudgetHolder::FILE_PATH . "/%")->count(),
      Category::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . Category::FILE_PATH . "/%")->count(),
      Distributor::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . Distributor::FILE_PATH . "/%")->count(),
      DistributorGroup::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . DistributorGroup::FILE_PATH . "/%")->count(),
      Divisi::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . Divisi::FILE_PATH . "/%")->count(),
      DocumentClaim::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . DocumentClaim::FILE_PATH . "/%")->count(),
      Investment::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . Investment::FILE_PATH . "/%")->count(),
      Product::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . Product::FILE_PATH . "/%")->count(),
      Region::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . Region::FILE_PATH . "/%")->count(),
      Spend::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . Spend::FILE_PATH . "/%")->count(),
      SubBrand::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . SubBrand::FILE_PATH . "/%")->count(),
      Tax::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . Tax::FILE_PATH . "/%")->count(),
      TipePromo::FILE_PATH => (clone $query)->where("public_path", "ILIKE", "%/" . TipePromo::FILE_PATH . "/%")->count(),
    ];

    return $this->response(compact('data_page', 'data_group'));
  }

  private function query($query)
  {
    return $query;
  }
}
