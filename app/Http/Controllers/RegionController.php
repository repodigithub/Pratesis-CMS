<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\Region;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RegionController extends MasterDataController
{
  public function __construct()
  {
    $this->middleware("auth:api",['except' => ['index']]);
    $this->model = Region::class;
    $this->model_key = "kode_region";
  }

  public function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("search")) {
      $query->where(function ($q) use ($req) {
        $q->where("kode_region", "ILIKE", "%{$req->query("search")}%");
        $q->orWhere("nama_region", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_region")) {
      $query->where("kode_region", "ILIKE", "%{$req->query("kode_region")}%");
    }

    if ($req->filled("nama_region")) {
      $query->where("nama_region", "ILIKE", "%{$req->query("nama_region")}%");
    }

    return $query;
  }

  public function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_region", $data)->first())) {
      $rules["kode_region"] = "required|unique:region,kode_region,$data->id";
    } else {
      $rules["kode_region"] = "required|unique:region";
    }
    $rules["nama_region"] = "required";
    return $rules;
  }
}
