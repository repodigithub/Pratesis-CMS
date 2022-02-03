<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Laravel\Lumen\Routing\Controller as BaseController;
use stdClass;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
            throw new BadRequestHttpException("$model not found.");
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
}
