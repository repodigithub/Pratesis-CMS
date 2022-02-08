<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\SpendType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SpendTypeController extends MasterDataController
{
  public function __construct()
  {
    $this->model = SpendType::class;
    $this->model_key = "kode_spend_type";
    $this->middleware("auth:api");
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("search")) {
      $query->where(function ($query) use ($req) {
        $query->where("kode_spend_type", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("kode_investment", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("fund_type", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("reference_tax", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("condition_type", "ILIKE", "%{$req->query("search")}%");
      });
    }
    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_spend_type", $data)->first())) {
      $rules["kode_spend_type"] = "required|unique:spend_type,kode_spend_type,$data->id";
    } else {
      $rules["kode_spend_type"] = "required|unique:spend_type";
    }
    $rules["kode_investment"] = "required|exists:investment,kode_investment";
    $rules["fund_type"] = "required|numeric";
    $rules["reference_tax"] = "required";
    $rules["condition_type"] = "required";
    return $rules;
  }
}
