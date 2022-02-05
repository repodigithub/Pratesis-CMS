<?php

namespace App\Http\Controllers;

use App\Mail\UserRegisteredMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
  const RECAPTCHA_URL = "https://www.google.com/recaptcha/api/siteverify";

  public function __construct()
  {
    $this->middleware("auth:api", ["except" => ["login", "register", "forgetPassword", "resetPassword"]]);
  }

  public function register(Request $req)
  {
    $this->validate($req, [
      "user_id" => "required|unique:user",
      "full_name" => "required",
      "email" => "email|required|unique:user",
      "password" => "required",
      "username" => "required|unique:user",
      "kode_distributor" => "nullable",
      "kode_area" => "nullable",
      "g-recaptcha-response" => "required"
    ]);

    $this->verifyCaptcha($req->input("g-recaptcha-response"));

    DB::transaction(function () use ($req) {
      $user = new User();
      $user->user_id = $req->input("user_id");
      $user->full_name = $req->input("full_name");
      $user->email = $req->input("email");
      $user->username = $req->input("username");
      $user->password = Hash::make($req->input("password"));
      $user->kode_distributor = $req->input("kode_distributor");
      $user->kode_area = $req->input("kode_area");
      $user->save();

      // Send mail
      Mail::to($user)->send(new UserRegisteredMail());
    });

    return $this->response([]);
  }

  public function login(Request $req)
  {
    $this->validate($req, [
      "email" => "email|required",
      "password" => "required",
      "g-recaptcha-response" => "required"
    ]);

    $this->verifyCaptcha($req->input("g-recaptcha-response"));

    $credentials = $req->only(["email", "password"]);

    if (!$token = auth()->attempt($credentials)) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "The provided credentials do not match our records.");
    }

    $user = Auth::user();
    $user->token = $token;

    return $this->response($user);
  }

  public function forgetPassword(Request $req)
  {
    $this->validate($req, [
      "email" => "email|required",
      "g-recaptcha-response" => "required"
    ]);

    $this->verifyCaptcha($req->input("g-recaptcha-response"));

    $status = Password::sendResetLink($req->only("email"), function ($user, $token) {
      Mail::to($user);
    });

    if ($status === Password::RESET_LINK_SENT) {
      return $this->response([]);
    }

    throw new HttpException(Response::HTTP_BAD_REQUEST, __($status));
  }

  public function resetPassword(Request $req)
  {
    $req->validate([
      "password" => "required|confirmed"
    ]);

    return $this->response([]);
  }

  public function me()
  {
    return $this->response(Auth::user());
  }

  public function refresh()
  {
    $token = Auth::refresh();
    $user = Auth::user();
    $user->token = $token;

    return $this->response($user);
  }

  public function updateProfile(Request $req)
  {
    $profile = Auth::user();

    $this->validate($req, [
      "user_id" => "required|unique:user,user_id,$profile->id",
      "full_name" => "required",
      "email" => "email|required|unique:user,email,$profile->id",
      "username" => "required|unique:user,username,$profile->id",
      "password" => "nullable",
      "kode_distributor" => "nullable",
      "kode_area" => "nullable",
      "kode_group" => "nullable"
    ]);

    $profile->user_id = $req->input("user_id");
    $profile->full_name = $req->input("full_name");
    $profile->email = $req->input("email");
    $profile->username = $req->input("username");
    if ($req->filled("password")) {
      $profile->password = Hash::make($req->input("password"));
    }
    $profile->kode_distributor = $req->input("kode_distributor");
    $profile->kode_area = $req->input("kode_area");
    $profile->kode_group = $req->input("kode_group");
    $profile->save();

    return $this->response($profile);
  }

  private function verifyCaptcha($grecaptcha)
  {
    $response = Http::asForm()->post(self::RECAPTCHA_URL, [
      "secret" => env("RECAPTCHA_SECRET"),
      "response" => $grecaptcha
    ])->json();

    if (!$response["success"]) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "CAPTCHA is not valid");
    }
  }
}
