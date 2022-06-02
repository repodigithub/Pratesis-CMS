<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\Distributor;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class DistributorController extends MasterDataController
{
  public function __construct()
  {
    $this->model = Distributor::class;
    $this->model_key = "kode_distributor";
    $this->middleware("auth:api", ["except" => "index"]);
    $this->middleware("signature", ["only" => "index"]);
  }

  protected function onFilter($data, $req)
  {
    if ($req->filled("search")) {
      $data->where(function ($query) use ($req) {
        $query->where("kode_distributor", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_distributor", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_distributor")) {
      $data->where("kode_distributor", "{$req->query("kode_distributor")}");
    }

    if ($req->filled("nama_distributor")) {
      $data->where("nama_distributor", "ILIKE", "%{$req->query("nama_distributor")}%");
    }

    if ($req->filled("kode_distributor_group")) {
      $data->where("kode_distributor_group", "{$req->query("kode_distributor_group")}");
    }

    if ($req->filled("kode_area")) {
      $data->where("kode_area", "{$req->query("kode_area")}");
    }

    if ($req->filled("kode_region")) {
      $data->whereHas("area", function ($q) use ($req) {
        $q->where("kode_region", "{$req->query("kode_region")}");
      });
    }

    if ($req->filled("alamat")) {
      $data->where("alamat", "ILIKE", "%{$req->query("alamat")}%");
    }

    if ($req->filled("status_distributor")) {
      $data->where("status_distributor", $req->query("status_distributor"));
    }

    return $data;
  }

  protected function getImportedData(Collection $collect)
  {
    $model = new $this->model;
    return $collect->map(function ($value) use ($model) {
      $data = [];
      foreach ($model->fillable as $index => $key) {
        if ($key == 'status' && in_array($value[$index], [0, 1])) {
          switch ($value[$index]) {
            case '0':
              $data[$key] = Distributor::STATUS_NON_ACTIVE;
              break;
            case '1':
              $data[$key] = Distributor::STATUS_ACTIVE;
              break;
            default:
              break;
          }
        } else {
          $data[$key] = $value[$index];
        }
      }
      return $data;
    });
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_distributor", $data)->first())) {
      $rules["kode_distributor"] = "required|unique:distributor,kode_distributor,$data->id";
    } else {
      $rules["kode_distributor"] = "required|unique:distributor";
    }
    $rules["nama_distributor"] = "required";
    $rules["kode_distributor_group"] = "required|exists:distributor_group,kode_distributor_group";
    $rules["kode_area"] = "required|exists:area,kode_area";
    $rules["alamat"] = "nullable";
    $rules["titik_koordinat"] = "nullable";
    $rules["status_distributor"] = ["required", Rule::in([Distributor::STATUS_ACTIVE, Distributor::STATUS_NON_ACTIVE, 0, 1])];
    return $rules;
  }
}
