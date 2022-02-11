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
    $this->model_key = "kode_kategori";
    $this->middleware("auth:api");
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("search")) {
      $query->where(function ($query) use ($req) {
        $query->where("kode_kategori", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_kategori", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_kategori")) {
      $query->where("kode_kategori", "ILIKE", "%{$req->query("kode_kategori")}%");
    }

    if ($req->filled("nama_kategori")) {
      $query->where("nama_kategori", "ILIKE", "%{$req->query("nama_kategori")}%");
    }

    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_kategori", $data)->first())) {
      $rules["kode_kategori"] = "required|unique:kategori,kode_kategori,$data->id";
    } else {
      $rules["kode_kategori"] = "required|unique:kategori";
    }
    $rules["nama_kategori"] = "required";
    return $rules;
  }
}
