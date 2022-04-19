<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    function __construct()
    {
        $this->middleware("auth:api");
    }

    public function getNote(Request $req)
    {
        return $this->response([
            'message_1' => Setting::get('message_1'),
            'message_2' => Setting::get('message_2'),
            'message_3' => Setting::get('message_3'),
        ]);
    }

    public function updateNote(Request $req)
    {
        return $this->response([
            'message_1' => Setting::set('message_1', $req->input('message_1')),
            'message_2' => Setting::set('message_2', $req->input('message_2')),
            'message_3' => Setting::set('message_3', $req->input('message_3')),
        ]);
    }

    public function getSign(Request $req)
    {
        $depot = auth()->user()->area;
        return $this->response([
            'created_by' => [
                "name" => Setting::get("depot_{$depot->id}_sign_1_name"),
                "sign" => Setting::get("depot_{$depot->id}_sign_1_image"),
            ],
            'approved_by' => [
                "name" => Setting::get("depot_{$depot->id}_sign_2_name"),
                "sign" => Setting::get("depot_{$depot->id}_sign_2_image"),
            ],
        ]);
    }

    public function updateSign(Request $req)
    {
        $depot = auth()->user()->area;

        $this->validate($req, [
            'created_by_name' => "required",
            'created_by_sign' => "nullable|file|image",
            'approved_by_name' => "required",
            'approved_by_sign' => "nullable|file|image",
        ]);

        $sign_1_name = "depot_{$depot->id}_sign_1_name";
        $sign_1_image = "depot_{$depot->id}_sign_1_image";
        $sign_2_name = "depot_{$depot->id}_sign_2_name";
        $sign_2_image = "depot_{$depot->id}_sign_2_image";

        Setting::set($sign_1_name, $req->input("created_by_name"));
        Setting::set($sign_2_name, $req->input("approved_by_name"));

        if ($req->hasFile('created_by_sign')) {
            $file = $req->file('created_by_sign');
            $filename = str_replace(" ", "_", $file->getClientOriginalName());
            $path = implode('/', ['setting', date('Ymd/His')]);
            $file->move(storage_path("app/public/" . $path), $filename);
            Setting::set($sign_1_image, url("storage/$path/$filename"));
        }

        if ($req->hasFile('approved_by_sign')) {
            $file = $req->file('approved_by_sign');
            $filename = str_replace(" ", "_", $file->getClientOriginalName());
            $path = implode('/', ['setting', date('Ymd/His')]);
            $file->move(storage_path("app/public/" . $path), $filename);
            Setting::set($sign_2_image, url("storage/$path/$filename"));
        }

        return $this->response([
            'created_by' => [
                "name" => Setting::get($sign_1_name),
                "sign" => Setting::get($sign_1_image),
            ],
            'approved_by' => [
                "name" => Setting::get($sign_2_name),
                "sign" => Setting::get($sign_2_image),
            ],
        ]);
    }

    public function getInvoiceNote(Request $req)
    {
        $depot = auth()->user()->area;
        if (empty($depot)) {
            $depot = auth()->user()->distributor->area;
        }

        return $this->response([
            "note" => [

                'message_1' => Setting::get('message_1'),
                'message_2' => Setting::get('message_2'),
                'message_3' => Setting::get('message_3'),
            ],
            "sign" => [
                'created_by' => [
                    "name" => Setting::get("depot_{$depot->id}_sign_1_name"),
                    "sign" => Setting::get("depot_{$depot->id}_sign_1_image"),
                ],
                'approved_by' => [
                    "name" => Setting::get("depot_{$depot->id}_sign_2_name"),
                    "sign" => Setting::get("depot_{$depot->id}_sign_2_image"),
                ],
            ]
        ]);
    }
}
