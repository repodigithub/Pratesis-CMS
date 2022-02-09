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
