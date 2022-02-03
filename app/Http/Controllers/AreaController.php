<?php

namespace App\Http\Controllers;

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

    DB::transaction(function () use ($req) {
      $file = $req->file("file");
      $filename = $file->getClientOriginalName();
      if (strpos($filename, Region::FILE_NAME) === false) {
        throw new BadRequestHttpException("Filename must \"" . Region::FILE_NAME . "\"");
      }
      $timestamp = date("Ymd-Gis");
      $path = "/" . implode("/", [Region::FILE_PATH, $timestamp]);
      $storage_path = "/app/public/$path";
      $public_path = "/storage/$path";
      $file_data = File::create([
        "title" => $filename,
        "type" => $file->getClientOriginalExtension(),
        "storage_path" => $storage_path,
        "public_path" => $public_path,
        "uploader_id" => Auth::user()->id
      ]);
      $regions = Excel::toCollection(new RegionsImport, $file)[0];
      $error = true;
      foreach ($regions as $key => $region) {
        if ($region["kode_region"] == "//END") {
          $regions->splice($key);
          $error = false;
          break;
        }
      }

      if ($error) {
        throw new BadRequestHttpException("no_end_tag_exception");
      }

      foreach ($regions as $key => $region) {
        $this->validate(new Request($region->toArray()), [
          "kode_region" => "required|unique:region",
          "nama_region" => "required",
        ], [
          "unique" => ":attribute \":input\" has already been taken.",
        ]);
        Region::create($region->toArray());
      }
      $file->move(storage_path($storage_path), $filename);
    });

    return $this->response();
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
