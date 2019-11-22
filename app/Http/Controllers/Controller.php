<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function respond($result, $message) {
        $response = [
            'status' => true,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    protected function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer'
        ]);
    }

    protected function error($message, $code) {
        $response = [
            'status' => false,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    protected function unauthorized() {
        $response = [
            'status' => false,
            'message' => 'Invalid credentials : authentication failed'
        ];

        return response()->json($response, 401);
    }
}


