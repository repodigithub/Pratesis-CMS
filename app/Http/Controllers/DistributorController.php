<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DistributorController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function index(Request $req)
  {
    $this->validate($req, [
      'status' => [Rule::in([Distributor::STATUS_ACTIVE, Distributor::STATUS_NON_ACTIVE])]
    ]);

    $pagination = $this->getPagination($req);

    $data = Distributor::select("*");

    if ($req->filled("search")) {
      $data->where(function ($query) use ($req) {
        $query->where("kode_distributor", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_distributor", "ILIKE", "%{$req->query("search")}%");
      });
    }

    if ($req->filled("kode_sales")) {
      $data->where("kode_sales_workforce", "ILIKE", $req->query("kode_sales"));
    }

    if ($req->filled("kode_area")) {
      $data->where("kode_area", "ILIKE", $req->query("kode_area"));
    }

    if ($req->filled("kode_region")) {
      $data->where("kode_region", "ILIKE", $req->query("kode_region"));
    }

    if ($req->filled("status")) {
      $data->where("status_distributor", "ILIKE", $req->query("status"));
    }

    $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

    return $this->response($data);
  }
}
