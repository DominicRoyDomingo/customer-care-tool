<?php

namespace CRM\API\Country;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CountryController extends Controller
{

   public function index(Request $request)
   {
      $lang = $request->lang;
      $countries = Country::select('id','name')->get();
      return response()->json($countries);
   }

   public function getPlaces(Request $request) 
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
                     'place' => $city->name.', '.$division->name.', '.$country->name, 
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
                  'place' => $city->name.', '.$division->name.', '.$country->name, 
                  'country_id' => $country->id,
                  'division_id' => $division->id,
                  'city_id' => $city->id,
               ]);
            };
         };
      };
      return response()->json($places);
   }

   public function getCities(Request $request) 
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

   public function getProvinces(Request $request) 
   {
      $search = $request->search;

      $countries = Country::select('id','name')->with('divisions')->orderBy('name', 'ASC')->get();

      $filteredCountries = $countries->filter(function($country) {
            return $country->divisions->count() > 0;
      });
      // dd();
      if($search != null) {
         $places = array();
         foreach($filteredCountries as $country) {
            foreach($country->divisions as $division) {
               array_push($places, [
                  'place' => $division->name.', '.$country->name, 
                  'country_id' => $country->id,
                  'division_id' => $division->id,
               ]);
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
            array_push($places, [
               'place' => $division->name.', '.$country->name, 
               'country_id' => $country->id,
               'division_id' => $division->id,
            ]);
         };
      };
      return response()->json($places);
   }

   public function getRegions(Request $request) 
   {
      $search = $request->search;

      $countries = Country::select('id','name')->with('regions')->orderBy('name', 'ASC')->get();
      $filteredCountries = $countries->filter(function($country) {
            return $country->regions->count() > 0;
      });
      // dd();
      if($search != null) {
         $places = array();
         foreach($filteredCountries as $country) {
            foreach($country->regions as $region) {
               array_push($places, [
                  'place' => $region->region.', '.$country->name, 
                  'country_id' => $country->id,
                  'region_id' => $region->id,
               ]);
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
         foreach($country->regions as $region) {
            array_push($places, [
               'place' => $region->region.', '.$country->name, 
               'country_id' => $country->id,
               'region_id' => $region->id,
            ]);
         };
      };
      return response()->json($places);
   }
}
