<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
                ->withAvg('reviews','rating')
                ->withCount('reviews')
                ->when(request()->q, function ($products) {
                    $products = $products->where('name','like','%'.request()->q.'%');
                })->latest()->paginate(8);
        return new ProductResource(true, 'List data Product', $products);
    }


    public function show($slug)
    {
        $product = Product::with('category','reviews.customer')
                ->withAvg('reviews','rating')
                ->withCount('reviews')
                ->where('slug', $slug)
                ->first();
        if ($product) {
            return new ProductResource(true, 'List data product', $product);
        }

        return new ProductResource(false,'Data tidak ditemukan',null);
    }
}
