<?php

namespace App\Http\Controllers;

use App\Models\Usergroup;

class UsergroupController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:api",['except' => ['index']]);
    }
    public function index()
    {
        $data = Usergroup::all();
        return $this->response($data);
    }
}