<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductController extends MasterDataController
{
  public function __construct()
  {
    $this->model = Product::class;
    $this->model_key = "kode_produk";
    $this->middleware("auth:api");
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("search")) {
      $query->where(function ($query) use ($req) {
        $query->where("kode_produk", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_produk", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_produk")) {
      $query->where("kode_produk", "ILIKE", "%{$req->query("kode_produk")}%");
    }

    if ($req->filled("nama_produk")) {
      $query->where("nama_produk", "ILIKE", "%{$req->query("nama_produk")}%");
    }

    if ($req->filled("kode_sub_brand")) {
      $query->where("kode_sub_brand", "ILIKE", "%{$req->query("kode_sub_brand")}%");
    }

    if ($req->filled("kode_brand")) {
      $query->where("kode_brand", "ILIKE", "%{$req->query("kode_brand")}%");
    }

    if ($req->filled("kode_kategori")) {
      $query->where("kode_kategori", "ILIKE", "%{$req->query("kode_kategori")}%");
    }

    if ($req->filled("kode_divisi")) {
      $query->where("kode_divisi", "ILIKE", "%{$req->query("kode_divisi")}%");
    }

    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_produk", $data)->first())) {
      $rules["kode_produk"] = "required|unique:produk,kode_produk,$data->id";
    } else {
      $rules["kode_produk"] = "required|unique:produk";
    }
    $rules["nama_produk"] = "required";
    $rules["kode_sub_brand"] = "required|exists:sub_brand,kode_sub_brand";
    $rules["kode_brand"] = "required|exists:brand,kode_brand";
    $rules["kode_kategori"] = "required|exists:kategori,kode_kategori";
    $rules["kode_divisi"] = "required|exists:divisi,kode_divisi";
    return $rules;
  }
}
