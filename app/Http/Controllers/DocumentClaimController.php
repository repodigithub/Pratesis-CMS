<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\DocumentClaim;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DocumentClaimController extends MasterDataController
{
  public function __construct()
  {
    $this->model = DocumentClaim::class;
    $this->model_key = "kode_dokumen";
    $this->middleware("auth:api");
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("search")) {
      $query->where(function ($q) use ($req) {
        $q->where("kode_dokumen", "ILIKE", "%{$req->query("search")}%");
        $q->orWhere("nama_dokumen", "ILIKE", "%{$req->query("search")}%");
        $q->orWhere("sumber_dokumen", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_dokumen")) {
      $query->where("kode_dokumen", "ILIKE", "%{$req->query("kode_dokumen")}%");
    }

    if ($req->filled("nama_dokumen")) {
      $query->where("nama_dokumen", "ILIKE", "%{$req->query("nama_dokumen")}%");
    }

    if ($req->filled("sumber_dokumen")) {
      $query->where("sumber_dokumen", "ILIKE", "%{$req->query("sumber_dokumen")}%");
    }

    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_dokumen", $data)->first())) {
      $rules["kode_dokumen"] = "required|unique:dokumen_klaim,kode_dokumen,$data->id";
    } else {
      $rules["kode_dokumen"] = "required|unique:dokumen_klaim";
    }
    $rules["nama_dokumen"] = "required";
    $rules["sumber_dokumen"] = "required";
    return $rules;
  }
}
