<?php

namespace CRM\API\Algolia;

use App\Http\Controllers\Controller;
use App\Models\Algolia\AlgoliaPermission;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AlgoliaPermissionController extends Controller
{
   
   public function checkPermission(Request $request)
   {
      $brandID = $request->brand_id;

      $algoliaPermission = AlgoliaPermission::where('brand', $brandID)->first();

      if($algoliaPermission) {
         if($algoliaPermission->isAllowed) return response()->json(true);
      }

      return response()->json(false);
   }
}
