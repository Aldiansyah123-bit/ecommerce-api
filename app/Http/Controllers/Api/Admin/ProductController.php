<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('category')->when(request()->q, function($products){
            $products = $products->where('title','like','%'.request()->q.'q');
        })->latest()->paginate(5);

        return new ProductResource(true, 'List Data Products', $products);
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
            'image'         => 'required|image|mimes:png,jpg|max:2000',
            'title'         => 'required',
            'category_id'   => 'required',
            'description'   => 'required',
            'weight'        => 'required',
            'price'         => 'required',
            'stock'         => 'required',
            'discount'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        $image = $request->file('image');
        $image->storeAs('public/products/',$image->hashName());

        $product = Product::create([
            'image'         => $image->hashName(),
            'title'         => $request->title,
            'slug'          => Str::slug($request->title, '-'),
            'category_id'   => $request->category_id,
            'user_id'       => auth()->guard('api_admin')->user()->id,
            'description'   => $request->description,
            'weight'        => $request->weight,
            'price'         => $request->price,
            'stock'         => $request->stock,
            'discount'      => $request->discount,
        ]);

        if ($product) {
            return new ProductResource(true, 'Data Product Berhasil di Simpan',$product);
        }
        return new ProductResource(false, 'Data Product Gagal di Simpan',null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);

        if ($product) {
            return new ProductResource(true,'List Data',$product);
        }
        return new ProductResource(false,'Data Tidak di Temukan',null);
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
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(),[
            // 'image'         => 'required|image|mimes:png,jpg|max:2000',
            'title'         => 'required|unique:products,title,'.$product->id,
            'description'   => 'required',
            'category_id'   => 'required',
            'weight'        => 'required',
            'price'         => 'required',
            'stock'         => 'required',
            'discount'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        if ($request->file('image')) {
            Storage::disk('local')->delete('public/products/'.basename($product->image));

            $image = $request->file('image');
            $image->storeAs('public/products',$image->hashName());

            $product->update([
                'image'         => $image->hashName(),
                'title'         => $request->title,
                'slug'          => Str::slug($request->title, '-'),
                'category_id'   => $request->category_id,
                'user_id'       => auth()->guard('api-admin')->user()->id,
                'discription'   => $request->description,
                'weight'        => $request->weight,
                'price'         => $request->price,
                'discount'      => $request->discount,
                'stock'         => $request->stock
            ]);
        }

        $product->update([
            'title'         => $request->title,
            'slug'          => Str::slug($request->title, '-'),
            'category_id'   => $request->category_id,
            'user_id'       => auth()->guard('api_admin')->user()->id,
            'description'   => $request->description,
            'weight'        => $request->weight,
            'price'         => $request->price,
            'discount'      => $request->discount,
            'stock'         => $request->stock,
        ]);

        if ($product) {
            return new ProductResource(true,'Data Berhasil di Update',$product);
        }
        return new ProductResource(false,'Data Gagal di Update',null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        Storage::disk('local')->delete('public/products/'.basename($product->image));
        if ($product) {
            return new ProductResource(true,'Data Berhasil di Hapus',null);
        }
        return new ProductResource(false,'Data Gagal di Hapus',null);
    }
}
