<?php

namespace App\Http\Controllers;

use App\Imports\MasterDataImport;
use App\Imports\RegionsImport;
use App\Models\File;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RegionController extends Controller
{
  public function __construct()
  {
    $this->middleware("auth:api");
  }

  public function index(Request $req)
  {
    $pagination = $this->getPagination($req);

    $data = Region::select("*");

    if ($req->filled("search")) {
      $data->where(function ($query) use ($req) {
        $query->where("kode_region", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_region", "ILIKE", "%{$req->query("search")}%");
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
      "kode_region" => "required|unique:region",
      "nama_region" => "required",
    ]);

    $data = Region::create($req->all());

    return $this->response($data);
  }

  public function upload(Request $req)
  {
    $this->validate($req, [
      "file" => "required|file|mimes:xlsx"
    ]);

    $regions = DB::transaction(function () use ($req) {
      $file = $req->file('file');
      $file_data = $this->storeFile(Region::class, $file);

      $import = new MasterDataImport();
      $import->onlySheets(Region::WORKSHEET_NAME);

      $regions = Excel::toCollection($import, $file)[Region::WORKSHEET_NAME];

      $first_region = $regions[0];
      if ($first_region[0] != Region::FIELD_NAME[0]) {
        throw new BadRequestHttpException("first_column_exception");
      }
      if ($first_region[1] != Region::FIELD_NAME[1]) {
        throw new BadRequestHttpException("second_column_exception");
      }

      $regions->splice(0, 1);
      $error = true;
      foreach ($regions as $key => $region) {
        if ($region[0] == "//END") {
          $regions->splice($key);
          $error = false;
          break;
        }
      }

      if ($error) {
        throw new BadRequestHttpException("no_end_tag_exception");
      }

      $regions = $regions->map(function ($row) {
        return [
          "kode_region" => $row[0],
          "nama_region" => $row[1],
        ];
      });

      $regions_data = collect();
      foreach ($regions as $key => $region) {
        $this->validate(new Request($region), [
          "kode_region" => "required|unique:region",
          "nama_region" => "required",
        ], [
          "required" => "The :attribute #" . ($key + 1) . " field is required",
          "unique" => "The :attribute #" . ($key + 1) . " with value \":input\" has already been taken.",
        ]);
        $regions_data->push(Region::create($region));
      }
      $file->move(storage_path($file_data->storage_path), $file_data->filename . '.' . $file_data->type);
      return $regions_data;
    });

    return $this->response($regions);
  }

  public function show($id, Request $req)
  {
    $data = $this->getModel(Region::class, $id);
    return $this->response($data);
  }

  public function update($id, Request $req)
  {
    $data = $this->getModel(Region::class, $id);

    $this->validate($req, [
      "kode_region" => "required|unique:region,kode_region,$data->id",
      "nama_region" => "required",
    ]);

    $data->update($req->all());

    return $this->response($data);
  }

  public function delete($id, Request $req)
  {
    $data = $this->getModel(Region::class, $id);
    $data->delete();
    return $this->response();
  }
}
