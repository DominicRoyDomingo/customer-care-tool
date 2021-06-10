<?php

namespace CRM\API\Parameter;

use App\Http\Controllers\Controller;
use App\Models\Parameter;
use App\Http\Requests\Backend\Parameter\StoreParameterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ParameterController extends Controller
{

   public function index(Request $request)
   {
      $lang = $request->lang;
      $entries = $request->entries;
      $page = (int)$request->page;
      $search = $request->search;
      $sortDesc = $request->sort_desc == 'true' ? true: false;

      $parameters = Parameter::select('id','name','created_at','updated_at');

      if($search != null) {
         $parameters = $parameters->where('name', 'LIKE', '%' . $search . '%')
         ->orWhere('created_at', 'LIKE', '%' . $search . '%')
         ->orWhere('updated_at', 'LIKE', '%' . $search . '%');
      }

      $parameters = $parameters->get();

      $parameters = $parameters->map(function ($parameter) use($lang){
         return [
               'id' => $parameter['id'], 
               'name' => $parameter['name'], 
               'created_at' => $parameter['created_at']->toDateString(), 
               'updated_at' => $parameter['updated_at']->toDateString(),
         ];
      });

      switch ($request->sort_name) {
         case 'name':
            $parameters = $parameters->sortBy('name', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'created_at':
            $parameters = $parameters->sortBy('created_at', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'updated_at':
            $parameters = $parameters->sortBy('updated_at', SORT_NATURAL, $sortDesc)->values();
            break;
      }
      if($entries != null && $page != null) {
         $parameters = $this->paginate($parameters, $entries, $page);
      }  
      return response()->json($parameters);
   }

   // for Category here
   public function store(StoreParameterRequest $request)
   {
      $lang = $request->lang;
      // dd($request->file('images'));
      if (is_null($request->id)) {

         $parameter = new Parameter;

         $parameter->name = $request->name;

         $parameter->save();

      } else {
         
         $parameter = Parameter::findOrFail($request->id);

         $parameter->name = $request->name;

         $parameter->update();
      }
      
      return response()->json($parameter);
   }

   public function destroy(Request $request)
   {
      Parameter::where('id',$request->id)->delete();

      return response()->json(true);
   }

   public function paginate($items, $perPage = 10, $page = 1)
   {

      $items = $items instanceof Collection ? $items : Collection::make($items);
      $currentPageResults = $items->slice(($page - 1) * $perPage, $perPage)->values();
      return new LengthAwarePaginator($currentPageResults, $items->count(), $perPage);
   }

}
