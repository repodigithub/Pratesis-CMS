<?php

namespace App\Http\Controllers;

use App\Imports\MasterDataImport;
use App\Models\DistributorGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DistributorGroupController extends Controller
{
  public function __construct()
  {
    $this->middleware("auth:api");
  }

  public function index(Request $req)
  {
    $pagination = $this->getPagination($req);

    $data = DistributorGroup::select("*");

    if ($req->filled("search")) {
      $data->where(function ($query) use ($req) {
        $query->where("kode_distributor_group", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_distributor_group", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if (!empty($pagination->sort)) {
      $sort = $pagination->sort;
      $data->orderBy($sort[0], $sort[1]);
    }

    $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

    return $this->response($data);
  }

  public function create(Request $req)
  {
    $this->validate($req, [
      "kode_distributor_group" => "required|unique:distributor_group",
      "nama_distributor_group" => "required",
    ]);

    $data = DistributorGroup::create($req->all());

    return $this->response($data);
  }

  public function upload(Request $req)
  {
    $this->validate($req, [
      "file" => "required|file|mimes:xlsx"
    ]);

    $data = DB::transaction(function () use ($req) {
      $file = $req->file('file');
      $file_data = $this->storeFile(DistributorGroup::class, $file);

      $import = new MasterDataImport();
      $import->onlySheets(DistributorGroup::WORKSHEET_NAME);

      $imported_data = Excel::toCollection($import, $file)[DistributorGroup::WORKSHEET_NAME];

      $data = $imported_data[0];
      foreach (DistributorGroup::FIELD_NAME as $key => $field) {
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

      $imported_data = $imported_data->map(function ($row) {
        return [
          "kode_distributor_group" => $row[0],
          "nama_distributor_group" => $row[1],
        ];
      });

      $data = 0;
      foreach ($imported_data as $key => $value) {
        $this->validate(new Request($value), [
          "kode_distributor_group" => "required",
          "nama_distributor_group" => "required",
        ], [
          "required" => "The :attribute #" . ($key + 1) . " field is required",
          "unique" => "The :attribute #" . ($key + 1) . " with value \":input\" has already been taken.",
        ]);
        DistributorGroup::updateOrCreate([
          "kode_distributor_group" => $value['kode_distributor_group'],
        ], $value);
        $data++;
      }
      $file->move(storage_path($file_data->storage_path), $file_data->filename . '.' . $file_data->type);
      return $data;
    });

    return $this->response($data);
  }

  public function show($id, Request $req)
  {
    $data = $this->getModel(DistributorGroup::class, $id);

    return $this->response($data);
  }

  public function update($id, Request $req)
  {
    $data = $this->getModel(DistributorGroup::class, $id);

    $this->validate($req, [
      "kode_distributor_group" => "required|unique:distributor_group,kode_distributor_group,$data->id",
      "nama_distributor_group" => "required"
    ]);

    $data->update($req->all());

    return $this->response($data);
  }

  public function delete($id, Request $req)
  {
    $data = $this->getModel(DistributorGroup::class, $id);
    $data->delete();
    return $this->response();
  }
}
