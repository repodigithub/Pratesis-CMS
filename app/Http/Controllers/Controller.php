<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function response($data, $message = 'OK')
    {
        return response()->json([
            'code' => Response::HTTP_OK,
            'message' => $message,
            'data' => $data
        ]);
    }
}
