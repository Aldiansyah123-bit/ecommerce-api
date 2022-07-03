<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::when(request()->q, function($categories){
            $categories = $categories->where('name','like','%'.request()->q.'%');
        })->latest()->paginate(5);

        return new CategoryResource(true, 'List Data Categories',$categories);
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
            'image'     => 'required|image|mimes:png,jpg|max:2000',
            'name'      => 'required|unique:categories'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/categories',$image->hashName());

        $category = Category::create([
            'image' => $image->hashName(),
            'name'  => $request->name,
            'slug'  => Str::slug($request->name, '-')
        ]);

        if ($category) {
            return new CategoryResource(true, 'Data Category Berhasil di Simpan', $category);
        }

        return new CategoryResource(false, 'Data Gagal di Simpan',null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::findOrFail($id)->first();

        if ($category) {
            # code...
            return new CategoryResource(true,'Detail Category', $category);
        }

        return new CategoryResource(false,'Detail Category tidak ada', $category);
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
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(),[
            'name'  => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        if ($request->file('image')) {
            Storage::disk('local')->delete('public/categories/'.basename($category->image));

            $file = $request->file('image');
            $file->storeAs('public/categories',$file->hashName());

            $category->update([
                'name'  => $request->name,
                'image' => $file->hashName(),
                'slug'  => Str::slug($request->name, '-')
            ]);
        }
        $category->update([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            return new CategoryResource(true, 'Data Berhasil di Update', $category);
        }

        return new CategoryResource(false,'Data Gagal di Update', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        Storage::disk('local')->delete('public/categories/'.basename($category->image));

        if ($category->delete()) {
            return new CategoryResource(true, 'Data Berhasil di Hapus',null);
        }
        return new CategoryResource(false, 'Data Gagal di Hapus',null);
    }
}
