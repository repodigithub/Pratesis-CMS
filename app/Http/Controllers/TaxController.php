<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TaxController extends MasterDataController
{
  public function __construct()
  {
    $this->model = Tax::class;
    $this->model_key = "kode_pajak";
    $this->middleware("auth:api");
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("search")) {
      $query->where(function ($q) use ($req) {
        $q->where("kode_pajak", "ILIKE", "%{$req->query("search")}%");
        $q->orWhere("nama_pajak", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_pajak")) {
      $query->where("kode_pajak", "ILIKE", "%{$req->query("kode_pajak")}%");
    }

    if ($req->filled("nama_pajak")) {
      $query->where("nama_pajak", "ILIKE", "%{$req->query("nama_pajak")}%");
    }

    if ($req->filled("tipe_pajak")) {
      $query->where("tipe_pajak", "ILIKE", "%{$req->query("tipe_pajak")}%");
    }

    if ($req->filled("presentase_pajak")) {
      $query->where("presentase_pajak", "ILIKE", "%{$req->query("presentase_pajak")}%");
    }

    if ($req->filled("reference_tax")) {
      $query->where("reference_tax", "ILIKE", "%{$req->query("reference_tax")}%");
    }

    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_pajak", $data)->first())) {
      $rules["kode_pajak"] = "required|unique:tax,kode_pajak,$data->id";
    } else {
      $rules["kode_pajak"] = "required|unique:tax";
    }
    $rules["nama_pajak"] = "required";
    $rules["tipe_pajak"] = "required";
    $rules["presentase_pajak"] = "required";
    $rules["reference_tax"] = "required";
    return $rules;
  }
}
