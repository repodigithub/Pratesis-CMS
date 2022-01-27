<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Laravel\Lumen\Routing\Controller as BaseController;
use stdClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Controller extends BaseController
{
    protected function response($data = [], $message = 'OK')
    {
        return response()->json([
            'code' => Response::HTTP_OK,
            'message' => $message,
            'data' => $data
        ]);
    }

    protected function getModel($model, $id)
    {
        try {
            $data = $model::findOrFail($id);
            return $data;
        } catch (\Throwable $th) {
            throw new NotFoundHttpException("$model not found.");
        }
    }

    protected function getPagination(Request $req)
    {
        $pagination = new stdClass();
        $pagination->page = $req->query("page") ?: 1;
        $pagination->limit = $req->query("limit") ?: 10;
        $pagination->sort = ['created_at', 'desc'];
        if ($req->filled("sort")) {
            $pagination->sort = explode(",", $req->query("sort"));
        }

        return $pagination;
    }
}
