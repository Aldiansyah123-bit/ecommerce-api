<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::when(request()->q, function($users){
            $users = $users->where('name','like','%'.request()->q.'%');
        })->latest()->paginate(5);

        return new UserResource(true,'List Data User',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);

        if ($user) {
            return new UserResource(true, 'Data Berhasil di Tambah',$user);
        }
        return new UserResource(false, 'Data Gagal di Tambah',null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::whereId($id)->first();

        if ($user) {
            return new UserResource(true, 'Detail data Users', $user);
        }
        return new UserResource(false, 'Detail data tidak ditemukan',null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(),[
            'name'      => 'required',
            'email'     => 'required|unique:users,email,'.$user->id,
            'password'  => 'confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        if ($request->password == "") {
            $user->update([
                'name'  => $request->name,
                'email' => $request->email,
            ]);
        }

        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
        ]);

        if ($user) {
            return new UserResource(true,'Data Berhasil di Update',$user);
        }
        return new UserResource(false,'Data Gagal di Update',null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->delete()) {
            return new UserResource(true, 'Data Berhasil di Hapus',null);
        }
        return new UserResource(false, 'Data Gagal di Hapus',null);
    }
}
