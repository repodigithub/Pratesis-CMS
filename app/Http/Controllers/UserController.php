<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware("auth:api");
  }


  public function index(Request $req)
  {
    $this->validate($req, [
      "status" => [Rule::in([User::STATUS_APPROVE, User::STATUS_PENDING, User::STATUS_REJECT])]
    ]);

    $pagination = $this->getPagination($req);

    $data = User::select("*");

    if ($req->filled("status")) {
      $data->whereStatus($req->query("status"));
    }

    if ($req->filled("kode_pengguna")) {
      $data->where("user_id", "ILIKE", "%{$req->query("kode_pengguna")}%");
    }

    if ($req->filled("kode_group")) {
      $data->where("kode_group", $req->query("kode_group"));
    }

    if (!empty($pagination->sort)) {
      $sort = $pagination->sort;
      $data->orderBy($sort[0], $sort[1]);
    }

    $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

    return $this->response($data);
  }

  public function action(Request $req)
  {
    $this->validate($req, [
      "ids" => "required|array",
      "action" => ["required", Rule::in([User::STATUS_APPROVE, User::STATUS_REJECT])]
    ], [
      "in" => "The :attribute must be one of the following types: :values"
    ]);

    $users = [];
    foreach ($req->input("ids") as $id) {
      $users[] = User::findOrFail($id);
    }

    User::whereIn("id", $req->input("ids"))->update([
      "status" => $req->input("action")
    ]);

    return $this->response();
  }

  public function show($id, Request $req)
  {
    $data = $this->getModel(User::class, $id);

    return $this->response($data);
  }

  public function update($id, Request $req)
  {
    $data = $this->getModel(User::class, $id);

    $this->validate($req, [
      "created_at" => "required|date",
      "user_id" => "required|unique:user,user_id,$data->id",
      "full_name" => "required",
      "email" => "email|required|unique:user,email,$data->id",
      "username" => "required|unique:user,username,$data->id",
      "kode_group" => "nullable",
      "kode_area" => "nullable",
      "kode_distributor" => "nullable",
      "status" => ["required", Rule::in([User::STATUS_APPROVE, User::STATUS_REJECT, User::STATUS_PENDING])],
    ]);

    $data->update($req->only([
      "created_at",
      "user_id",
      "full_name",
      "email",
      "username",
      "kode_group",
      "kode_area",
      "kode_distributor",
      "status",
    ]));

    return $this->response($data);
  }
}
