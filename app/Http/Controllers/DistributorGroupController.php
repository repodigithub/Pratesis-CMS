<?php

namespace App\Http\Controllers;

use App\Imports\RegionsImport;
use App\Models\DistributorGroup;
use App\Models\File;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $query->where("kode_sales_workforce", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_sales_workforce", "ILIKE", "%{$req->query("search")}%");
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
      "kode_sales_workforce" => "required|unique:sales_workforce",
      "nama_sales_workforce" => "required",
    ]);

    $data = DistributorGroup::create($req->all());

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
    $data = $this->getModel(DistributorGroup::class, $id);

    return $this->response($data);
  }

  public function update($id, Request $req)
  {
    $data = $this->getModel(DistributorGroup::class, $id);

    $this->validate($req, [
      "kode_sales_workforce" => "required|unique:sales_workforce,kode_sales_workforce,$data->id",
      "nama_sales_workforce" => "required"
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
