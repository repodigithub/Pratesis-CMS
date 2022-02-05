<?php

namespace App\Http\Controllers;

use App\Imports\MasterDataImport;
use App\Imports\RegionsImport;
use App\Models\Area;
use App\Models\File;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AreaController extends Controller
{
  public function __construct()
  {
    $this->middleware("auth:api");
  }

  public function index(Request $req)
  {
    $pagination = $this->getPagination($req);

    $data = Area::select("*");

    if ($req->filled("include")) {
      $data->with($req->query("include"));
    }

    if ($req->filled("region")) {
      $data->where("kode_region", $req->query("region"));
    }

    if ($req->filled("search")) {
      $data->where(function ($query) use ($req) {
        $query->where("kode_area", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_area", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("alamat_depo", "ILIKE", "%{$req->query("search")}%");
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
      "kode_area" => "required|unique:area",
      "nama_area" => "required",
      "alamat_depo" => "required",
      "kode_region" => "required|exists:region,kode_region",
      "koordinat" => "nullable"
    ]);

    $data = Area::create($req->all());

    return $this->response($data);
  }

  public function upload(Request $req)
  {
    $this->validate($req, [
      "file" => "required|file|mimes:xlsx"
    ]);


    $data = DB::transaction(function () use ($req) {
      $file = $req->file('file');
      $file_data = $this->storeFile(Area::class, $file);

      $import = new MasterDataImport();
      $import->onlySheets(Area::WORKSHEET_NAME);

      $imported_data = Excel::toCollection($import, $file)[Area::WORKSHEET_NAME];

      $data = $imported_data[0];
      foreach (Area::FIELD_NAME as $key => $field) {
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
          "kode_area" => $row[0],
          "nama_area" => $row[1],
          "alamat_depo" => $row[2],
          "kode_region" => $row[3],
          "koordinat" => $row[4],
        ];
      });

      $data = 0;
      foreach ($imported_data as $key => $value) {
        $this->validate(new Request($value), [
          "kode_area" => "required",
          "nama_area" => "required",
          "alamat_depo" => "required",
          "kode_region" => "required|exists:region,kode_region",
          "koordinat" => "nullable"
        ], [
          "required" => "The :attribute #" . ($key + 1) . " field is required",
          "unique" => "The :attribute #" . ($key + 1) . " with value \":input\" has already been taken.",
        ], [
          "kode_area" => "Kode Area",
          "nama_area" => "Nama Area",
          "alamat_depo" => "Alamat",
          "kode_region" => "Kode Region",
          "koordinat" => "Titik Koordinat",
        ]);
        Area::updateOrCreate([
          "kode_area" => $value["kode_area"]
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
    $data = $this->getModel(Area::class, $id, $req->query("include"));

    return $this->response($data);
  }

  public function update($id, Request $req)
  {
    $data = $this->getModel(Area::class, $id);

    $this->validate($req, [
      "kode_area" => "required|unique:area,kode_area,$data->id",
      "nama_area" => "required",
      "alamat_depo" => "required",
      "kode_region" => "required|exists:region,kode_region",
      "koordinat" => "nullable"
    ]);

    $data->update($req->all());

    return $this->response($data);
  }

  public function delete($id, Request $req)
  {
    $data = $this->getModel(Area::class, $id);
    $data->delete();
    return $this->response();
  }
}
