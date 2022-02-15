<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\Alasan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AlasanController extends MasterDataController
{
  public function __construct()
  {
    $this->model = Alasan::class;
    $this->model_key = "kode_alasan";
    $this->middleware("auth:api");
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("search")) {
      $query->where(function ($q) use ($req) {
        $q->where("kode_alasan", "ILIKE", "%{$req->query("search")}%");
        $q->orWhere("deskripsi_alasan", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_alasan")) {
      $query->where("kode_alasan", "ILIKE", "%{$req->query("kode_alasan")}%");
    }

    if ($req->filled("deskripsi_alasan")) {
      $query->where("deskripsi_alasan", "ILIKE", "%{$req->query("deskripsi_alasan")}%");
    }

    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_alasan", $data)->first())) {
      $rules["kode_alasan"] = "required|unique:alasan,kode_alasan,$data->id";
    } else {
      $rules["kode_alasan"] = "required|unique:alasan";
    }
    $rules["deskripsi_alasan"] = "required";
    return $rules;
  }
}
