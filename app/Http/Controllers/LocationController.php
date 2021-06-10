<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientLocation;
use Khsing\World\World;
use Khsing\World\Models\Country;
use Khsing\World\Models\Division;
use Khsing\World\Models\City;

class LocationController extends Controller
{
    public function get_countries(){
        $countries = World::Countries();
        
        return response()->json($countries);
    }

    public function get_provinces($id){
        $country = Country::findOrfail($id);
        $provinces = $country->divisions()->get();
        return response()->json($provinces);
    }

    public function get_cities($id){
        $province = Division::findOrfail($id);
        $cities = $province->cities()->get();
        return response()->json($cities);
    }

    public function add_province(Request $request, $id){
        $province = Division::create([
            'country_id' => $id,
            'name' => $request->name,
            'full_name' => null,
            'code' => "0",
            'has_city' => "1",
        ]);

        return response()->json($province);
    }

    public function add_city(Request $request, $id){
        $province = Division::findOrfail($id);
        $city = City::create([
            'country_id' => $province->country_id,
            'division_id' => $id,
            'name' => $request->name,
            'full_name' => null,
            'code' => null
        ]);

        return response()->json($city);
    }

}
