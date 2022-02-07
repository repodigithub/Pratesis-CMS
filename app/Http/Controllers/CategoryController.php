<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CategoryController extends MasterDataController
{
  public function __construct()
  {
    $this->model = Category::class;
    $this->model_key = "kode_category";
    $this->middleware("auth:api");
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("search")) {
      $query->where(function ($query) use ($req) {
        $query->where("kode_category", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_category", "ILIKE", "%{$req->query("search")}%");
      });
    }
    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_category", $data)->first())) {
      $rules["kode_category"] = "required|unique:kategori,kode_category,$data->id";
    } else {
      $rules["kode_category"] = "required|unique:kategori";
    }
    $rules["nama_category"] = "required";
    return $rules;
  }
}
