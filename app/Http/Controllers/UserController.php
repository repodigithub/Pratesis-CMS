<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;

class UserController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  
  public function index(Request $req)
  {
  }

  public function action(Request $req)
  {
  }

  public function show($id, Request $req)
  {
  }

  public function update($id, Request $req)
  {
  }
}
