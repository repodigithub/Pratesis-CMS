<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\Divisi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DivisiController extends MasterDataController
{
  public function __construct()
  {
    $this->model = Divisi::class;
    $this->model_key = "kode_divisi";
    $this->middleware("auth:api");
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("search")) {
      $query->where(function ($query) use ($req) {
        $query->where("kode_divisi", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_divisi", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_divisi")) {
      $query->where("kode_divisi", "ILIKE", "%{$req->query("kode_divisi")}%");
    }

    if ($req->filled("nama_divisi")) {
      $query->where("nama_divisi", "ILIKE", "%{$req->query("nama_divisi")}%");
    }

    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_divisi", $data)->first())) {
      $rules["kode_divisi"] = "required|unique:divisi,kode_divisi,$data->id";
    } else {
      $rules["kode_divisi"] = "required|unique:divisi";
    }
    $rules["nama_divisi"] = "required";
    return $rules;
  }
}
