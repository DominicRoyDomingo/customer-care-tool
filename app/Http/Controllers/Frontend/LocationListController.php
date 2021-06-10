<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Division;
use App\Models\City;
use Illuminate\Http\Request;

class LocationListController extends Controller
{
    public function countries(Request $request)
    {
        $countries = Country::all();

        return response()->json($countries);
    }

    public function divisions(Request $request)
    {
        $divisions = Division::all();

        return response()->json($divisions);
    }

    public function cities(Request $request)
    {
        $cities = City::all();

        return response()->json($cities);
    }
}
