<?php

namespace App\Http\Controllers;

use App\Models\Permission\Group;
use App\Models\Permission\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserGroupController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function index(Request $req)
  {

    $pagination = $this->getPagination($req);

    $data = Group::with('permissions');

    if ($req->filled("search")) {
      $data->where(function ($query) use ($req) {
        $query->where("kode_group", "ILIKE", "%{$req->query("search")}%");
        $query->orWhere("nama_group", "ILIKE", "%{$req->query("search")}%");
      });
    }

    // if ($req->filled("kode_sales")) {
    //   $data->where("kode_sales_workforce", "ILIKE", $req->query("kode_sales"));
    // }

    // if ($req->filled("kode_area")) {
    //   $data->where("kode_area", "ILIKE", $req->query("kode_area"));
    // }

    // if ($req->filled("kode_region")) {
    //   $data->where("kode_region", "ILIKE", $req->query("kode_region"));
    // }

    // if ($req->filled("status")) {
    //   $data->where("status_distributor", "ILIKE", $req->query("status"));
    // }

    if ($req->filled("sort")) {
      $sort = explode(",", $req->query("sort"));
      $data->orderBy($sort[0], $sort[1]);
    }

    $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

    return $this->response($data);
  }

  public function create(Request $req)
  {
    $this->validate($req, [
      'kode_group' => 'required|unique:user_group',
      'nama_group' => 'required',
      'permission_ids' => 'array|nullable'
    ]);

    $data = DB::transaction(function () use ($req) {
      $group = Group::create($req->only(['kode_group', 'nama_group']));
      if ($req->filled('permission_ids')) {
        $permissions = Permission::whereIn('id', $req->input('permission_ids'))->get();
        $group->permissions()->attach($permissions);
      }
      $group->permissions;
      return $group;
    });

    return $this->response($data);
  }

  public function show($id, Request $req)
  {
    $data = $this->getModel(Group::class, $id);
    $data->permissions;
    return $this->response($data);
  }

  public function update($id, Request $req)
  {
    $this->validate($req, [
      'kode_group' => 'required|unique:user_group',
      'nama_group' => 'required',
      'permission_ids' => 'array|nullable'
    ]);

    $data = DB::transaction(function () use ($req, $id) {
      $group = $this->getModel(Group::class, $id);

      $group->update($req->only(['kode_group', 'nama_group']));
      if ($req->filled('permission_ids')) {
        $permissions = Permission::whereIn('id', $req->input('permission_ids'))->get();
        $group->permissions()->sync($permissions);
      }
      $group->permissions;
      return $group;
    });

    return $this->response($data);
  }

  public function delete($id, Request $req)
  {
    $group = $this->getModel(Group::class, $id);
    $group->delete();
    return $this->response();
  }

  public function attach($id, Request $req)
  {
    $this->validate($req, [
      'permission_ids' => 'required|array'
    ]);

    $group = $this->getModel(Group::class, $id);
    $permissions = Permission::whereIn('id', $req->input('permission_ids'))->get();

    $group->permissions()->attach($permissions);

    return $this->response($group->permissions);
  }

  public function detach($id, Request $req)
  {
    $this->validate($req, [
      'permission_ids' => 'required|array'
    ]);

    $group = $this->getModel(Group::class, $id);
    $permissions = Permission::whereIn('id', $req->input('permission_ids'))->get();

    $group->permissions()->detach($permissions);

    return $this->response($group->permissions);
  }
}
