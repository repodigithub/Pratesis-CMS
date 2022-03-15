<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\Promo\Promo;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PromoController extends MasterDataController
{
  public function __construct()
  {
    $this->model = Promo::class;
    $this->middleware("auth:api");
  }

  public function show($id, Request $req)
  {
    $include = null;
    if ($req->filled('include')) {
      $include = $req->query('include');
    }

    $data = $this->getModel($this->model, $id, $include, ['statistics']);

    return $this->response($data);
  }

  public function generateID()
  {
    $opso_id = date('y') . date('m') . str_pad(Promo::count() + 1, 4, 0, STR_PAD_LEFT);
    return $this->response(compact('opso_id'));
  }

  public function updateStatus($id, Request $req)
  {
    $data = $this->getModel($this->model, $id);

    $this->validate($req, [
      'status' => ['nullable', Rule::in([Promo::STATUS_APPROVE, Promo::STATUS_DRAFT, Promo::STATUS_NEED_APPROVAL, Promo::STATUS_REJECT])]
    ]);

    $data->status = $req->input('status');
    $data->save();

    return $this->response($data);
  }

  protected function onFilter(Builder $query, Request $req)
  {
    if ($req->filled("nama")) {
      $query->where("nama_promo", "ILIKE", "%{$req->query("nama")}%");
    }

    if ($req->filled("opso_id")) {
      $query->where("opso_id", "ILIKE", "%{$req->query("opso_id")}%");
    }

    if ($req->filled("area_id")) {
      $query->whereHas("promoArea", function ($q) use ($req) {
        $q->where('kode_area', $req->query("area_id"));
      });
    }

    if ($req->filled("start_date")) {
      $query->whereDate("start_date", date('Y-m-d', strtotime($req->query("start_date"))));
    }

    if ($req->filled("end_date")) {
      $query->whereDate("end_date", date('Y-m-d', strtotime($req->query("end_date"))));
    }

    if ($req->filled("spend_type")) {
      $query->where("kode_spend_type", $req->query("spend_type"));
    }

    if ($req->filled("status")) {
      $query->where("status", $req->query("status"));
    }

    return $query;
  }

  protected function rules($data = null)
  {
    $rules = [];
    if (!empty($data->id) || !empty($data = $this->model::where("opso_id", $data)->first())) {
      $rules["opso_id"] = "required|unique:promo,opso_id,$data->id";
    } else {
      $rules["opso_id"] = "required|unique:promo";
      $rules['file'] = 'required|file';
    }
    $rules['nama_promo'] = 'required';
    $rules['budget'] = 'required|numeric';
    $rules['status'] = ['nullable', Rule::in([Promo::STATUS_APPROVE, Promo::STATUS_DRAFT, Promo::STATUS_NEED_APPROVAL, Promo::STATUS_REJECT])];
    $rules['start_date'] = 'required|date|before:end_date';
    $rules['end_date'] = 'required|date|after:start_date';
    $rules['claim'] = 'required|numeric';
    $rules['kode_spend_type'] = 'required|exists:spend_type,kode_spend_type';
    $rules['kode_budget_holder'] = 'required|exists:budget_holder,kode_budget_holder';
    return $rules;
  }

  protected function beforeUpdateOrCreate(Request $req)
  {
    $data = $req->all();

    $file = $req->file('file');
    if (!empty($file)) {
      $file_name = $file->getClientOriginalName();
      $file_path = implode("/", ['promo', date('Ymd/His')]);
      $file->move(storage_path('/app/public/' . $file_path), $file_name);

      $data['file'] = "/storage/${file_path}/{$file_name}";
    }

    if (empty($data['status'])) {
      $data['status'] = Promo::STATUS_DRAFT;
    }

    return new Request($data);
  }

  protected function afterDelete($data)
  {
    File::delete(storage_path(str_replace('storage', 'app/public', $data->file)));
  }
}
