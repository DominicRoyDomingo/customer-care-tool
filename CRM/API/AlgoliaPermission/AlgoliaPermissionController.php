<?php

namespace CRM\API\AlgoliaPermission;

use App\Http\Controllers\Controller;
use App\Models\Algolia\AlgoliaPermission;
use App\Http\Requests\Backend\AlgoliaPermission\StoreAlgoliaPermissionRequest;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AlgoliaPermissionController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->lang;
        $entries = $request->entries;
        $page = (int)$request->page;
        $search = $request->search;
        $isSearched = $request->isSearched;
        $sortDesc = $request->sort_desc == 'true' ? true: false;
        $algoliaPermissions = AlgoliaPermission::select('id', 'class', 'brand', 'isAllowed', 'index_name')->with(['brand_item', 'class_item']);

        $algoliaPermissions->when($request->hasGroup, function ($q) use($request) {
            return $q->orderBy('group_id', 'DESC');
        });

        if($search != null) {
            // dd($isSearched);
            $algoliaPermissions = $algoliaPermissions->where(function($subQuery) use ($search) {
                $subQuery->whereHas('brand_item', function ($query) use ($search) {
                    $query->where('name', $search);
                })->orWhereHas('class_item', function ($query) use ($search) {
                    $query->where('name', $search);
                });
            });
        }
        
        $algoliaPermissions = $algoliaPermissions->paginate($entries);
        return $algoliaPermissions;
    }

    public function store(StoreAlgoliaPermissionRequest $request)
    {

        if (is_null($request->id)) {
            $algoliaPermission = AlgoliaPermission::with(['brand_item', 'class_item'])->create([
                "class"  => $request->class,
                "brand"  => $request->brand,
                "isAllowed" => $request->sync ? 1 : 0,
                "index_name" => json_encode([
                    'live' => $request->live_index_name,
                    'staging' => $request->staging_index_name,
                ]),
            ]);

        } else {

            $algoliaPermission = AlgoliaPermission::with(['brand_item', 'class_item'])->findOrFail($request->id);

            $algoliaPermission->update([
                "class"  => $request->class,
                "brand"  => $request->brand,
                "isAllowed" => $request->sync ? 1 : 0,
                "index_name" => json_encode([
                    'live' => $request->live_index_name,
                    'staging' => $request->staging_index_name,
                ]),
            ]);

            $algoliaPermission = AlgoliaPermission::with(['brand_item', 'class_item'])->findOrFail($request->id);

        }

        return response()->json($algoliaPermission);
    }

    public function destroy(Request $request)
    {
        AlgoliaPermission::where('id',$request->id)->delete();

        return response()->json(true);
    }

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
