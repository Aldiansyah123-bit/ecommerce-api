<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\RajaOngkirResource;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
    public function getProvince()
    {
        $provinces = Province::all();
        return new RajaOngkirResource(true,'List Data Province', $provinces);
    }

    public function getCities(Request $request)
    {
        $province = Province::where('province_id', $request->province_id)->first();

        $cities = City::where('province_id', $request->province_id)->get();

        return new RajaOngkirResource(true, 'List Data City By Province : ' .$province->name. '',$cities);
    }

    public function checkOngkir(Request $request)
    {
        $response = Http::withHeaders([
            'key'   => config('services.rajaongkir.key')
        ])->post('https://api.rajaongkir.com/starter/cost',[
            'origin'        => 113,
            'destination'   => $request->destination,
            'weight'        => $request->weight,
            'courier'       => $request->courier,
        ]);

        return new RajaOngkirResource(true, 'List Data Biaya Ongkos Kirim : '.$request->courier. '', $response['rajaongkir']['results'][0]['costs']);
    }
}
