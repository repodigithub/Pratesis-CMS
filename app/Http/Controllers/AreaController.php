<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\Area;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AreaController extends MasterDataController
{
  public function __construct()
  {
    $this->model = Area::class;
    $this->model_key = "kode_area";
    $this->middleware("auth:api", ["except" => "index"]);
    $this->middleware("signature", ["only" => "index"]);
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("include")) {
      $query->with($req->query("include"));
    }

    if ($req->filled("search")) {
      $query->where(function ($q) use ($req) {
        $q->where("kode_area", "ILIKE", "%{$req->query("search")}%");
        $q->orWhere("nama_area", "ILIKE", "%{$req->query("search")}%");
        $q->orWhere("alamat_depo", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("region")) {
      $query->where("kode_region", $req->query("region"));
    }

    if ($req->filled("kode_area")) {
      $query->where("kode_area", $req->query("kode_area"));
    }

    if ($req->filled("nama_area")) {
      $query->where("nama_area", $req->query("nama_area"));
    }

    if ($req->filled("alamat_depo")) {
      $query->where("alamat_depo", $req->query("alamat_depo"));
    }

    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_area", $data)->first())) {
      $rules["kode_area"] = "required|unique:area,kode_area,$data->id";
    } else {
      $rules["kode_area"] = "required|unique:area";
    }
    $rules["nama_area"] = "required";
    $rules["alamat_depo"] = "nullable";
    $rules["kode_region"] = "required|exists:region,kode_region";
    $rules["titik_koordinat"] = "nullable";
    return $rules;
  }
}
