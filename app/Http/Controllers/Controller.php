<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Controller extends BaseController
{
    protected function response($data = [], $message = "OK")
    {
        return response()->json([
            "code" => Response::HTTP_OK,
            "message" => $message,
            "data" => $data
        ]);
    }

    protected function getModel($model, $id, $include = null)
    {
        try {
            $data = $model::where('id', $id);
            if (!empty($include)) {
                $data->with($include);
            }
            if (empty($data->count())) {
                throw new \Exception("$model not found", 1);
            }
            return $data->first();
        } catch (\Throwable $th) {
            throw new NotFoundHttpException("$model not found.");
        }
    }

    protected function getPagination(Request $req)
    {
        $pagination = new stdClass();
        $pagination->page = $req->query("page") ?: 1;
        $pagination->limit = $req->query("limit") ?: 10;
        $pagination->sort = ["created_at", "desc"];
        if ($req->filled("sort")) {
            $pagination->sort = explode(",", $req->query("sort"));
        }

        return $pagination;
    }

    protected function storeFile($model, $file)
    {
        $filename = $file->getClientOriginalName();
        if (strpos($filename, $model::FILE_NAME) === false) {
            throw new BadRequestHttpException("Filename must \"" . $model::FILE_NAME . "\"");
        }
        $timestamp = date('Ymd-His');
        $path = "/" . implode("/", [$model::FILE_PATH, $timestamp]);
        $storage_path = "/app/public/$path";
        $public_path = "/storage/$path";
        $file_data = File::create([
            "title" => $filename,
            "type" => $file->getClientOriginalExtension(),
            "storage_path" => $storage_path,
            "public_path" => $public_path,
            "uploader_id" => Auth::user()->id
        ]);
        return $file_data;
    }
}
