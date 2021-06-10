<?php

namespace CRM\API\Location;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Location\City\StoreCityRequest;
use App\Http\Requests\Backend\Location\Division\StoreDivisionRequest;
use App\Http\Requests\Backend\Location\Region\StoreRegionRequest;
use App\Models\ClientLocation;
use App\Models\Region;
use Khsing\World\World;
use Khsing\World\Models\Country;
use Khsing\World\Models\Division;
use Khsing\World\Models\City;

class LocationController extends Controller
{
    //Test   
    public function get_countries(){
        $countries = World::Countries();
        $countries = $countries->sortBy('name')->values();

        return response()->json($countries);
    }

    public function get_regions($id){
        $regions = [];

        if($id == "all"){
            $regions = Region::orderBy('region', 'ASC')->get();
        }
        else{
            $country = App\Models\Country::findOrfail($id);
            $regions = $country->divisions()->get();
        }

        return response()->json($regions);
    }

    public function get_provinces($id){
        $provinces = [];

        if($id == "all"){
            $provinces = Division::with('country')->orderBy('name', 'ASC')->get();
        }
        else{
            $country = Country::findOrfail($id);
            $provinces = $country->divisions()->get();
        }

        return response()->json($provinces);
    }

    public function get_cities($id){
        if($id == "all"){
            $cities = City::with('division')->with('country')->get();
        }
        else{
            $province = Division::findOrfail($id);
            $cities = $province->cities()->get();
        }
        return response()->json($cities);
    }

    public function add_province(StoreDivisionRequest $request){

        $province = new Division;
        $province->country_id =  $request->country_id;
        $province->name =  $request->name;
        $province->full_name =  null;
        $province->code =  0;
        $province->has_city = 1;
        $province->timestamps = false;
        $province->save();

        return response()->json($province);
    }

    public function update_province(Request $request, $id){
        $request->validate([
           'name' => 'required'
        ]);
        
        $province = Division::findOrfail($id);
        $province->name = $request->name;
        $province->save();
        
        return response()->json($province);
    }

    public function delete_province(Request $request, $id){
        $province = Division::findOrfail($id);
        $province->delete();

        return response()->json(true);
    }

    public function add_city(StoreCityRequest $request){
        $request->validate([
           'name' => 'required'
        ]);

        $province = Division::findOrfail($request->province_id);
        $row = $province->first();  
        $check = City::where([
            ['country_id', '=', $row->country_id],
            ['division_id', '=', $row->id],
            ['name', '=', $request->name]
        ]);

        if($check->exists()){
            return response()->json(['status' => false]);
        }
        
        $city = new City;
        $city->country_id = $row->country_id;
        $city->division_id = $request->province_id;
        $city->name = $request->name;
        $city->full_name = $request->name;
        $city->code = null;
        $city->timestamps = false;
        
        if($city->save()){
             $city['status'] = true;
             return response()->json($city);
        }

       return response()->json(['status' => false]);
    }

    public function update_city(Request $request, $id){
        $request->validate([
           'name' => 'required'
        ]);
  
        $city = City::findOrfail($id);
        $city->name = $request->name;
        $city->save();
        
        return response()->json($city);
    }

    public function delete_city(Request $request, $id){
        $city = City::findOrfail($id);
        $city->delete();

        return response()->json(true);
    }

    public function add_region(StoreRegionRequest $request){
  
        $region = Region::create([
            'country' => $request->country_id,
            'region' => $request->region,
        ]);
        
        $region->country = $region->country;

        return response()->json($region);
    }

    public function update_region(Request $request, $id){
        $request->validate([
           'region' => 'required'
        ]);
        
        $region = Region::findOrfail($id);
        $region->region = $request->region;
        $region->save();
        
        return response()->json($region);
    }

    public function delete_region(Request $request, $id){
        $region = Region::findOrfail($id);
        $region->delete();

        return response()->json(true);
    }

    public function filter_regions(Request $request){
        $regions = Region::
        whereHas('country', function($q) use ($request) {
            $q->where('name', 'LIKE', '%' . $request->country . '%');
        })
        ->where('region', 'LIKE', '%' . $request->region . '%')
        ->get();

        return response()->json($regions);
    }

    public function filter_provinces(Request $request){
        $provinces = Division::
        whereHas('country', function($q) use ($request) {
            $q->where('name', 'LIKE', '%' . $request->country . '%');
        })
        ->where('name', 'LIKE', '%' . $request->province . '%')
        ->get();

        return response()->json($provinces);
    }

    public function filter_cities(Request $request){
        $cities = City::
        whereHas('country', function($q) use ($request) {
            $q->where('name', 'LIKE', '%' . $request->country . '%');
        })
        ->whereHas('division', function($q) use ($request) {
            $q->where('name', 'LIKE', '%' . $request->province . '%');
        })
        ->where('name', 'LIKE', '%' . $request->city . '%')
        ->get();

        return response()->json($cities);
    }

    public function fetch_region($id){
        $region = Region::with('country')->where('id', $id)->first();

        return response()->json($region);
    }


    public function fetch_province($id){
        $province = Division::with('country')->with('cities')->where('id', $id)->first();

        return response()->json($province);
    }


    public function fetch_city($id){
        $city = City::with('country')->with('division')->where('id', $id)->first();

        return response()->json($city);
    }
}
