<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\Investment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class InvestmentController extends MasterDataController
{
  public function __construct()
  {
    $this->model = Investment::class;
    $this->model_key = "kode_investment";
    $this->middleware("auth:api");
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("search")) {
      $query->where(function ($q) use ($req) {
        $q->where("kode_investment", "ILIKE", "%{$req->query("search")}%");
        $q->orWhere("nama_investment", "ILIKE", "%{$req->query("search")}%");
      });
    }

    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_investment", $data)->first())) {
      $rules["kode_investment"] = "required|unique:investment,kode_investment,$data->id";
    } else {
      $rules["kode_investment"] = "required|unique:investment";
    }
    $rules["nama_investment"] = "required";
    return $rules;
  }
}
