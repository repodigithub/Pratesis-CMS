<?php

namespace App\Http\Controllers;

use App\Imports\MasterDataImport;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DistributorController extends Controller
{
  public function __construct()
  {
    $this->middleware("auth:api");
  }

  public function index(Request $req)
  {
    $this->validate($req, [
      "status" => [Rule::in([Distributor::STATUS_ACTIVE, Distributor::STATUS_NON_ACTIVE])]
    ]);

    $pagination = $this->getPagination($req);

    $data = Distributor::select("*");

    if ($req->filled("search")) {
      $data->where(function ($query) use ($req) {
        $query->where("kode_distributor", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_distributor", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_distributor_group")) {
      $data->where("kode_distributor_group", "ILIKE", $req->query("kode_distributor_group"));
    }

    if ($req->filled("kode_area")) {
      $data->where("kode_area", "ILIKE", $req->query("kode_area"));
    }

    if ($req->filled("status")) {
      $data->where("status_distributor", "ILIKE", $req->query("status"));
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
    $this->validate($req, $this->rules());

    $data = Distributor::create($req->all());

    return $this->response($data);
  }

  public function upload(Request $req)
  {
    $this->validate($req, [
      "file" => "required|file|mimes:xlsx"
    ]);

    $data = DB::transaction(function () use ($req) {
      $file = $req->file('file');
      $file_data = $this->storeFile(Distributor::class, $file);

      $import = new MasterDataImport();
      $import->onlySheets(Distributor::WORKSHEET_NAME);

      $imported_data = Excel::toCollection($import, $file)[Distributor::WORKSHEET_NAME];

      $data = $imported_data[0];
      foreach (Distributor::FIELD_NAME as $key => $field) {
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
          "kode_distributor" => $row[0],
          "nama_distributor" => $row[1],
          "kode_distributor_group" => $row[2],
          "kode_area" => $row[3],
          "alamat" => $row[4],
          "titik_koordinat" => $row[5],
          "status_distributor" => $row[6],
        ];
      });

      $data = 0;
      foreach ($imported_data as $key => $value) {
        $this->validate(new Request($value), $this->rules($value["kode_distributor"]), [
          "required" => "The :attribute #" . ($key + 1) . " field is required",
          "unique" => "The :attribute #" . ($key + 1) . " with value \":input\" has already been taken.",
          "exists" => "The :attribute #" . ($key + 1) . " invalid.",
        ]);

        Distributor::updateOrCreate([
          "kode_distributor" => $value['kode_distributor'],
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
    $data = $this->getModel(Distributor::class, $id);

    return $this->response($data);
  }

  public function update($id, Request $req)
  {
    $data = $this->getModel(Distributor::class, $id);

    $this->validate($req, $this->rules($data));

    $data->update($req->all());

    return $this->response($data);
  }

  public function delete($id, Request $req)
  {
    $data = $this->getModel(Distributor::class, $id);
    $data->delete();
    return $this->response();
  }

  private function rules($data = null)
  {
    $rules = [];
    if (!empty($data)) {
      if (empty($data->id)) {
        $data = Distributor::where("kode_distributor", $data)->first();
      }
      if (!empty($data)) {
        $rules["kode_distributor"] = "required|unique:distributor,kode_distributor,$data->id";
      } else {
        $rules["kode_distributor"] = "required|unique:distributor";
      }
    } else {
      $rules["kode_distributor"] = "required|unique:distributor";
    }
    $rules["nama_distributor"] = "required";
    $rules["kode_distributor_group"] = "required|exists:distributor_group,kode_distributor_group";
    $rules["kode_area"] = "required|exists:area,kode_area";
    $rules["alamat"] = "required";
    $rules["titik_koordinat"] = "nullable";
    $rules["status_distributor"] = ["required", Rule::in([Distributor::STATUS_ACTIVE, Distributor::STATUS_NON_ACTIVE])];
    return $rules;
  }
}
