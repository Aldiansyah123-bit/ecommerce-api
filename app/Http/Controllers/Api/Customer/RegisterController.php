<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'      => 'required',
            'email'     => 'required|email|unique:customers',
            'password'  => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        $customer = Customer::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
        ]);

        if ($customer) {
            return new CustomerResource(true,'Register Customer Berhasil',$customer);
        }
        return new CustomerResource(false,'Register Customer Gagal',null);
    }
}
