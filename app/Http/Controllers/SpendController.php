<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\Spend;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SpendController extends MasterDataController
{
  public function __construct()
  {
    $this->model = Spend::class;
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

    if ($req->filled("kode_spend_type")) {
      $query->where("kode_spend_type", "ILIKE", "%{$req->query("kode_spend_type")}%");
    }

    if ($req->filled("kode_investment")) {
      $query->where("kode_investment", "ILIKE", "%{$req->query("kode_investment")}%");
    }

    if ($req->filled("fund_type")) {
      $query->where("fund_type", "ILIKE", "%{$req->query("fund_type")}%");
    }

    if ($req->filled("reference_tax")) {
      $query->where("reference_tax", "ILIKE", "%{$req->query("reference_tax")}%");
    }

    if ($req->filled("condition_type")) {
      $query->where("condition_type", "ILIKE", "%{$req->query("condition_type")}%");
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
    $rules["fund_type"] = "required";
    $rules["reference_tax"] = "required";
    $rules["condition_type"] = "required";
    return $rules;
  }
}
