<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Imports\MasterDataImport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MasterDataController extends Controller
{
  protected $model;
  protected $model_key;

  public function index(Request $req)
  {
    $pagination = $this->getPagination($req);

    $data = $this->model::select("*");

    $data = $this->onFilter($data, $req);

    if (!empty($pagination->sort)) {
      $sort = $pagination->sort;
      $data->orderBy($sort[0], $sort[1]);
    }

    $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

    return $this->response($data);
  }

  public function create(Request $req)
  {
    $this->validate($req, $this->rules());

    $data = $this->model::create($req->all());

    return $this->response($data);
  }

  public function upload(Request $req)
  {
    $this->validate($req, [
      "file" => "required|file|mimes:xlsx"
    ]);

    $data = DB::transaction(function () use ($req) {
      $file = $req->file('file');
      $file_data = $this->storeFile($this->model, $file);

      $import = new MasterDataImport();
      $import->onlySheets($this->model::WORKSHEET_NAME);

      $imported_data = Excel::toCollection($import, $file)[$this->model::WORKSHEET_NAME];

      $data = $imported_data[0];
      foreach ($this->model::FIELD_NAME as $key => $field) {
        if ($data[$key] != $field) {
          throw new BadRequestHttpException("#{$key}_column_error");
        }
      }

      $imported_data->splice(0, 1);
      $error = true;
      foreach ($imported_data as $key => $value) {
        if ($value[0] == "//END") {
          $imported_data->splice($key);
          $error = false;
          break;
        }
      }

      if ($error) {
        throw new BadRequestHttpException("no_end_tag_error");
      }

      $imported_data = $this->getImportedData($imported_data);

      $data = 0;
      foreach ($imported_data as $key => $value) {
        $this->validate(new Request($value), $this->rules($value[$this->model_key]), [
          "required" => "The :attribute #" . ($key + 1) . " field is required",
          "unique" => "The :attribute #" . ($key + 1) . " with value \":input\" has already been taken.",
          "exists" => "The :attribute #" . ($key + 1) . " with value \":input\" is invalid.",
        ]);
        $this->model::updateOrCreate([
          $this->model_key => $value[$this->model_key]
        ], $value);
        $data++;
      }
      $file->move(storage_path($file_data->storage_path), $file_data->title);
      return $data;
    });

    return $this->response($data);
  }

  public function show($id, Request $req)
  {
    $data = $this->getModel($this->model, $id);

    return $this->response($data);
  }

  public function update($id, Request $req)
  {
    $data = $this->getModel($this->model, $id);

    $this->validate($req, $this->rules($data));

    $data->update($req->all());

    return $this->response($data);
  }

  public function delete($id, Request $req)
  {
    $data = $this->getModel($this->model, $id);
    $data->delete();
    return $this->response();
  }

  // reuseable

  protected function onFilter(Builder $query, Request $req)
  {
    return $query;
  }

  protected function getImportedData(Collection $collect)
  {
    $model = new $this->model;
    return $collect->map(function ($value) use ($model) {
      $data = [];
      foreach ($model->fillable as $index => $key) {
        $data[$key] = $value[$index];
      }
      return $data;
    });
  }

  protected function rules($data = null)
  {
    $rules = [];
    return $rules;
  }
}
