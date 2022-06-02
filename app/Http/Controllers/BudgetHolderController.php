<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\BudgetHolder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BudgetHolderController extends MasterDataController
{
  public function __construct()
  {
    $this->model = BudgetHolder::class;
    $this->model_key = "kode_budget_holder";
    $this->middleware("auth:api");
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("search")) {
      $query->where(function ($q) use ($req) {
        $q->where("kode_budget_holder", "ILIKE", "%{$req->query("search")}%");
        $q->orWhere("nama_budget_holder", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_budget_holder")) {
      $query->where("kode_budget_holder", "ILIKE", "%{$req->query("kode_budget_holder")}%");
    }

    if ($req->filled("nama_budget_holder")) {
      $query->where("nama_budget_holder", "ILIKE", "%{$req->query("nama_budget_holder")}%");
    }

    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_budget_holder", $data)->first())) {
      $rules["kode_budget_holder"] = "required|unique:budget_holder,kode_budget_holder,$data->id";
    } else {
      $rules["kode_budget_holder"] = "required|unique:budget_holder";
    }
    $rules["nama_budget_holder"] = "required";
    return $rules;
  }
}
