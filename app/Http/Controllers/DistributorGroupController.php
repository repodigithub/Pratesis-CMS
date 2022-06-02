<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Imports\MasterDataImport;
use App\Models\DistributorGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DistributorGroupController extends MasterDataController
{
  public function __construct()
  {
    $this->model = DistributorGroup::class;
    $this->model_key = "kode_distributor_group";
    $this->middleware("auth:api");
  }

  protected function onFilter($data, $req)
  {
    if ($req->filled("search")) {
      $data->where(function ($query) use ($req) {
        $query->where("kode_distributor_group", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_distributor_group", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_distributor_group")) {
      $data->where("kode_distributor_group", "ILIKE", "%{$req->query("kode_distributor_group")}%");
    }

    if ($req->filled("nama_distributor_group")) {
      $data->where("nama_distributor_group", "ILIKE", "%{$req->query("nama_distributor_group")}%");
    }

    return $data;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_distributor_group", $data)->first())) {
      $rules["kode_distributor_group"] = "required|unique:distributor_group,kode_distributor_group,$data->id";
    } else {
      $rules["kode_distributor_group"] = "required|unique:distributor_group";
    }
    $rules["nama_distributor_group"] = "required";
    return $rules;
  }
}
