<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\TipePromo;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TipePromoController extends MasterDataController
{
  public function __construct()
  {
    $this->middleware("auth:api");
    $this->model = TipePromo::class;
    $this->model_key = "kode_kegiatan";
  }

  public function onFilter($data, $req)
  {
    $data->with(['documents:id,kode_dokumen', 'spendTypes:id,kode_spend_type,kode_investment']);

    if ($req->filled("search")) {
      $data->where(function ($query) use ($req) {
        $query->where("kode_kegiatan", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_kegiatan", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_kegiatan")) {
      $data->where("kode_kegiatan", "ILIKE", "%{$req->query("kode_kegiatan")}%");
    }

    if ($req->filled("nama_kegiatan")) {
      $data->where("nama_kegiatan", "ILIKE", "%{$req->query("nama_kegiatan")}%");
    }

    if ($req->filled("spend_type")) {
      $data->whereHas("spendTypes", function ($query) use ($req) {
        $query->where('kode_spend_type', "ILIKE", "%{$req->query("kode_spend_type")}%");
      });
    }

    if ($req->filled("kode_investment")) {
      $data->where("kode_investment", "ILIKE", "%{$req->query("kode_investment")}%");
    }

    if ($req->filled("nama_investment")) {
      $data->whereHas("investment", function ($query) use ($req) {
        $query->where('nama_investment', "ILIKE", "%{$req->query("nama_investment")}%");
      });
    }

    if ($req->filled("kode_ppn")) {
      $data->where("kode_ppn", "ILIKE", "%{$req->query("kode_ppn")}%");
    }

    if ($req->filled("persentase_ppn")) {
      $data->whereHas("ppn", function ($query) use ($req) {
        $query->where('presentase_pajak', "ILIKE", "%{$req->query("persentase_ppn")}%");
      });
    }

    if ($req->filled("kode_pph")) {
      $data->where("kode_pph", "ILIKE", "%{$req->query("kode_pph")}%");
    }

    if ($req->filled("persentase_pph")) {
      $data->whereHas("pph", function ($query) use ($req) {
        $query->where('presentase_pajak', "ILIKE", "%{$req->query("persentase_pph")}%");
      });
    }

    return $data;
  }

  public function show($id, Request $req)
  {
    $include = 'documents;spendTypes';
    if ($req->filled('include')) {
      $include = $req->query('include');
    }

    $data = $this->getModel($this->model, $id, $include);

    return $this->response($data);
  }

  public function getImportedData(Collection $collect)
  {
    $model = new $this->model;
    return $collect->map(function ($value) use ($model) {
      $data = [];
      foreach ($model->fillable as $index => $key) {
        if ($index >= 5) {
          break;
        }
        $data[$key] = $value[$index];
      }
      $data['spend_types'] = $value->slice(5, 10)->filter()->toArray();
      $data['documents'] = $value->slice(15, 10)->filter()->toArray();
      return $data;
    });
  }

  protected function afterUpdateOrCreate($model, $req)
  {
    $model->spendTypes()->sync($req->input('spend_types'));
    $model->documents()->sync($req->input('documents'));
    return $this->getModel($this->model, $model->id);
  }

  public function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("kode_kegiatan", $data)->first())) {
      $rules["kode_kegiatan"] = "required|unique:tipe_promo,kode_kegiatan,$data->id";
    } else {
      $rules["kode_kegiatan"] = "required|unique:tipe_promo";
    }
    $rules["nama_kegiatan"] = "required";
    $rules["deskripsi_kegiatan"] = "required";
    $rules["kode_ppn"] = "nullable|exists:tax,kode_pajak";
    $rules["kode_pph"] = "nullable|exists:tax,kode_pajak";
    $rules["documents"] = "nullable|array";
    $rules["spend_types"] = "nullable|array";
    $rules["documents.*"] = "nullable|exists:dokumen_klaim,kode_dokumen";
    $rules["spend_types.*"] = "nullable|exists:spend_type,kode_spend_type";
    return $rules;
  }
}
