<?php

namespace CRM\API\City;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CityController extends Controller
{

   public function index(Request $request)
   {
      $lang = $request->lang;
      $countryOrDivision = $request->country != null || $request->country == true ? 'country_id' : 'division_id';
      $cities = City::select('id','name')->where($countryOrDivision, $request->id)->get();
      return response()->json($cities);
   }

   public function all(Request $request)
   {
      $search = $request->search;
      $countries = Country::select('id','name')->with('divisions','cities')->orderBy('name', 'ASC')->get();

      $filteredCountries = $countries->filter(function($country) {
            return $country->divisions->count() > 0;
      });
      // dd();
      if($search != null) {
         $places = array();
         foreach($filteredCountries as $country) {
            foreach($country->divisions as $division) {
               foreach($division->cities as $city) {
                  array_push($places, [
                     'place' => $city->name.', '.$country->name, 
                     'country_id' => $country->id,
                     'division_id' => $division->id,
                     'city_id' => $city->id,
                  ]);
               };
            };
         };
         $places = collect($places);

         $filteredPlaces = $places->filter(function($item) use ($search) {
            $place = str_replace(array(' ', ','), '' , $item['place']);
            $search = str_replace(array(' ', ','), '' , $search);
            
            return stripos($place, $search) !== false;
         });

         $filteredPlaces = $filteredPlaces->forPage($request->page, 15);

         return response()->json($filteredPlaces->values());
      }
      $filteredCountries = $filteredCountries->values();

      $filteredCountries = $filteredCountries->forPage($request->page, 1);
      // dd($filteredCountries);
      // $countries = $countries->paginate(1);
      $places = array();
      foreach($filteredCountries as $country) {
         foreach($country->divisions as $division) {
            foreach($division->cities as $city) {
               array_push($places, [
                  'place' => $city->name.', '.$country->name, 
                  'country_id' => $country->id,
                  'division_id' => $division->id,
                  'city_id' => $city->id,
               ]);
            };
         };
      };
      return response()->json($places);
   }
}
