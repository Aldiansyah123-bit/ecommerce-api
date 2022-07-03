<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        $credent = $request->only('email','password');


        if (!$token = auth()->guard('api_customer')->attempt($credent)) {
            return response()->json([
                'success' => false,
                'message' => 'Email or Password is incorrect'
            ],400);
        }

        return response()->json([
            'success'   => true,
            'user'      => auth()->guard('api_customer')->user(),
            'token'     => $token,
        ], 200);
    }


    public function getUser()
    {
        return response()->json([
            'success'   => true,
            'user'      => auth()->guard('api_customer')->user(),
        ], 200);
    }

    public function refreshToken(Request $request)
    {
        $refresh = JWTAuth::refresh(JWTAuth::getToken());

        $user = JWTAuth::setToken($refresh)->toUser();

        $request->headers->set('Authorization','Bearer'.$refresh);

        return response()->json([
            'success'   => true,
            'user'      => $user,
            'token'     => $refresh
        ], 200);
    }

    public function logout()
    {
        $remove =  JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success'   => true,
        ], 200);
    }

}
