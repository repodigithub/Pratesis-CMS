<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Controller extends BaseController
{
    public function response($data = [], $message = 'OK')
    {
        return response()->json([
            'code' => Response::HTTP_OK,
            'message' => $message,
            'data' => $data
        ]);
    }

    public function getModel($model, $id)
    {
        try {
            $data = $model::findOrFail($id);
            return $data;
        } catch (\Throwable $th) {
            throw new NotFoundHttpException("$model not found.");
        }
    }
}
