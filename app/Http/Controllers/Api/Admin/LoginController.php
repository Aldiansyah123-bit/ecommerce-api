<?php

namespace App\Http\Controllers\Api\Admin;

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

        if (!$token =  auth()->guard('api_admin')->attempt($credent)) {
            return response()->json([
                'success'   => false,
                'message'   => 'Email or Password incorrect'
            ],402);
        }

        $auth = auth()->guard('api_admin')->user();
        return response()->json([
            'success'   => true,
            'user'      => $auth,
            'token'     => $token,
        ],200);
    }

    public function getUser()
    {
        $auth = auth()->guard('api_admin')->user();

        return response()->json([
            'success'   => true,
            'user'      => $auth
        ],200);
    }

    public function refreshToken(Request $request)
    {
        $refresToken = JWTAuth::refresh(JWTAuth::getToken());

        $user = JWTAuth::setToken($refresToken)->toUser();

        $request->headers->set('Authorization','Bearer'.$refresToken);

        return response()->json([
            'success' => true,
            'user'    => $user,
            'token'   => $refresToken,
        ],200);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success' => true
        ],200);
    }
}
