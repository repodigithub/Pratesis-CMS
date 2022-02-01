<?php

namespace App\Http\Controllers;

use App\Models\Area;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:api",['except' => ['index']]);
    }
    public function index()
    {
        $data = Area::all();
        return $this->response($data);
    }
}