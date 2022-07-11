<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('id','DESC')->get();

        return new CategoryResource(true, 'List Data Cateogried', $categories);
    }

    public function show($slug)
    {
        $category = Category::with('products.category')
                   ->with('products', function($query){
                        $query->withCount('reviews');
                        $query->withAvg('reviews','rating');
                   })->where('slug', $slug)
                   ->first();
        if ($category) {
            return new CategoryResource(true, 'Data Product By Category : '.$category->name.'',$category);
        }

        return new CategoryResource(false, 'Data Category Tidak di Temukan',null);
    }

}
