<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
  const RECAPTCHA_URL = 'https://www.google.com/recaptcha/api/siteverify';

  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login', 'register', 'forgetPassword', 'resetPassword']]);
  }

  public function register(Request $req)
  {
    $this->validate($req, [
      'user_id' => 'required|unique:user',
      'full_name' => 'required',
      'email' => 'email|required|unique:user',
      'password' => 'required',
      'username' => 'required|unique:user',
      'kode_distributor' => 'required',
      'kode_area' => 'required',
      'g-recaptcha-response' => 'required'
    ]);

    $this->verifyCaptcha($req->input('g-recaptcha-response'));

    $user = new User();
    $user->user_id = $req->input('user_id');
    $user->full_name = $req->input('full_name');
    $user->email = $req->input('email');
    $user->username = $req->input('username');
    $user->password = Hash::make($req->input('password'));
    $user->kode_distributor = $req->input('kode_distributor');
    $user->kode_area = $req->input('kode_area');
    $user->save();

    return $this->response([]);
  }

  public function login(Request $req)
  {
    $this->validate($req, [
      'email' => 'email|required',
      'password' => 'required',
      'g-recaptcha-response' => 'required'
    ]);

    $this->verifyCaptcha($req->input('g-recaptcha-response'));

    $credentials = $req->only(['email', 'password']);

    if (Auth::attempt($credentials)) {
      $req->session()->regenerate();

      return redirect()->intended('dashboard');
    }

    throw new HttpException(Response::HTTP_BAD_REQUEST, "The provided credentials do not match our records.'");
  }

  public function me(Request $req)
  {
    $this->validate($req, [
      'index' => 'required'
    ]);
  }

  private function verifyCaptcha($grecaptcha)
  {
    $response = Http::post(self::RECAPTCHA_URL, [
      'secret' => env('RECAPTCHA_SECRET'),
      'response' => $grecaptcha
    ])->json();

    if (!$response['success']) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "CAPTCHA is not valid");
    }
  }
}
