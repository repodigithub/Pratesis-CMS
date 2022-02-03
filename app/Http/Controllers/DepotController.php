<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use Illuminate\Http\Request;

class DepotController extends Controller
{
  public function __construct()
  {
    $this->middleware("auth:api");
  }

  public function index(Request $req)
  {
    $pagination = $this->getPagination($req);

    $data = Depot::select("*");

    if ($req->filled("search")) {
      $data->where(function ($query) use ($req) {
        $query->where("kode_area", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_area", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("alamat_depo", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_region")) {
      $data->where("kode_region", "ILIKE", $req->query("kode_region"));
    }

    if (!empty($pagination->sort)) {
      $sort = $pagination->sort;
      $data->orderBy($sort[0], $sort[1]);
    }

    $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

    return $this->response($data);
  }
}
