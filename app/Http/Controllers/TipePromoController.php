<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\TipePromo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TipePromoController extends MasterDataController
{
  public function __construct()
  {
    $this->middleware("auth:api");
    $this->model = TipePromo::class;
    $this->model_key = "kode_kegiatan";
  }

  public function onFilter($query, $req)
  {
    $query->with('documents:id,kode_dokumen');
    $query->with('spendTypes:id,kode_spend_type,kode_investment');
    return $query;
  }

  protected function getModel($model, $id, $include = null)
  {
    try {
      $data = $model::where('id', $id);
      $data->with('documents:id,kode_dokumen');
      $data->with('spendTypes:id,kode_spend_type,kode_investment');

      if (empty($data->count())) {
        throw new \Exception("$model not found", 1);
      }
      return $data->first();
    } catch (\Throwable $th) {
      throw new NotFoundHttpException("$model not found.");
    }
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
