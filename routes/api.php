<?php

use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\InvoiceController;
use App\Http\Controllers\Api\Admin\LoginController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\SliderController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Api\Customer\InvoiceController as CustomerInvoiceController;
use App\Http\Controllers\Api\Customer\LoginController as CustomerLoginController;
use App\Http\Controllers\Api\Customer\RegisterController;
use App\Http\Controllers\Api\Customer\ReviewController;
use App\Http\Controllers\Api\Web\CategoryController as WebCategoryController;
use App\Http\Controllers\Api\Web\ProductController as WebProductController;
use App\Http\Controllers\Api\Web\RajaOngkirController;
use App\Http\Controllers\Api\Web\SliderController as WebSliderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('admin')->group(function  (){
    Route::post('/login',[LoginController::class, 'index', ['as' => 'admin']]);

    Route::group(['middleware' => 'auth:api_admin'],function () {
        Route::get('/user',[LoginController::class,'getUser', ['as' => 'admin']]);
        Route::get('/refresh',[LoginController::class, 'refreshToken', ['as' => 'admin']]);
        Route::post('/logout', [LoginController::class, 'logout',['as' => 'admin']]);
        Route::get('/dashboard',[DashboardController::class, 'index',['as' => 'admin']]);
        Route::apiResource('/categories',CategoryController::class, ['except' => ['create','edit'], 'as' => 'admin']);
        Route::apiResource('/products',ProductController::class,['except' => ['create','edit'], 'as' => 'admin']);
        Route::apiResource('invoices', InvoiceController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'admin']);
        Route::get('/customers', [CustomerController::class,'index', ['as' => 'admin']]);
        Route::apiResource('/sliders', SliderController::class,['except' => ['create','show','update','edit'], 'as' => 'admin']);
        Route::apiResource('users',UserController::class,['except' => ['create','edit'], 'as' => 'admin']);
    });
});

Route::prefix('customer')->group(function (){
    Route::post('/register',[RegisterController::class, 'store'], ['as' => 'customer']);
    Route::post('/login',[CustomerLoginController::class, 'index'], ['as' => 'customer']);

    Route::group(['middleware' => 'auth:api_customer'], function(){
        Route::get('/user', [CustomerLoginController::class, 'getUser'], ['as' => 'customer']);
        Route::get('/refresh', [CustomerLoginController::class, 'refreshToken'],['as' => 'customer']);
        Route::post('/logout', [CustomerLoginController::class, 'logout'],['as' => 'customer']);

        Route::get('/dashboard', [CustomerDashboardController::class, 'index'], ['as'=> 'customer']);
        Route::apiResource('/invoices', CustomerInvoiceController::class, ['except' => ['create','store','edit','update','destroy'],'as'=>'customer']);
        Route::post('reviews',[ReviewController::class, 'store'],['as'=>'customer']);
    });
});

Route::prefix('web')->group(function (){
    Route::apiResource('/categories', WebCategoryController::class, ['except' => ['create','store','edit','update','destroy'], 'as' => 'web']);
    Route::apiResource('products', WebProductController::class, ['except' => ['create','store','update','edit','destroy'], 'as' => 'web']);
    Route::get('sliders', [WebSliderController::class, 'index'],['as' => 'web']);
    Route::get('/rajaongkir/provinces', [RajaOngkirController::class,'getProvince'],['as'=>'web']);
    Route::post('rajaongkir/cities',[RajaOngkirController::class, 'getCities'],['as'=>'web']);
    Route::post('/rajaongkir/checkOngkir', [RajaOngkirController::class, 'checkOngkir'],['as' => 'web']);
});
