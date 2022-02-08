<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Imports\MasterDataImport;
use App\Models\SubBrand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SubBrandController extends MasterDataController
{
  public function __construct()
  {
    $this->model = SubBrand::class;
    $this->model_key = "kode_sub_brand";
    $this->middleware("auth:api");
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("search")) {
      $query->where(function ($q) use ($req) {
        $q->where("kode_sub_brand", "ILIKE", "%{$req->query("search")}%");
        $q->orWhere("nama_sub_brand", "ILIKE", "%{$req->query("search")}%");
      });
    }

    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_sub_brand", $data)->first())) {
      $rules["kode_sub_brand"] = "required|unique:sub_brand,kode_sub_brand,$data->id";
    } else {
      $rules["kode_sub_brand"] = "required|unique:sub_brand";
    }
    $rules["nama_sub_brand"] = "required";
    return $rules;
  }
}
