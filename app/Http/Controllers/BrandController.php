<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BrandController extends MasterDataController
{
  public function __construct()
  {
    $this->model = Brand::class;
    $this->model_key = "kode_brand";
    $this->middleware("auth:api");
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("search")) {
      $query->where(function ($query) use ($req) {
        $query->where("kode_brand", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_brand", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_brand")) {
      $query->where("kode_brand", "ILIKE", "%{$req->query("kode_brand")}%");
    }

    if ($req->filled("nama_brand")) {
      $query->where("nama_brand", "ILIKE", "%{$req->query("nama_brand")}%");
    }

    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_brand", $data)->first())) {
      $rules["kode_brand"] = "required|unique:brand,kode_brand,$data->id";
    } else {
      $rules["kode_brand"] = "required|unique:brand";
    }
    $rules["nama_brand"] = "required";
    return $rules;
  }
}
