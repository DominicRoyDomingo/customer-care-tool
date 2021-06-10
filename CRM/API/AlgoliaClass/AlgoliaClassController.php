<?php

namespace CRM\API\AlgoliaClass;

use App\Http\Controllers\Controller;
use App\Models\Algolia\AlgoliaClass;
use App\Http\Requests\Backend\AlgoliaClass\StoreAlgoliaClassRequest;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AlgoliaClassController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->lang;
        $entries = $request->entries;
        $page = (int)$request->page;
        $search = $request->search;
        $isSearched = $request->isSearched;
        $sortDesc = $request->sort_desc == 'true' ? true: false;
        $algoliaClasses = AlgoliaClass::select('id', 'name', 'created_at');

        $algoliaClasses->when($request->hasGroup, function ($q) use($request) {
            return $q->orderBy('group_id', 'DESC');
        });

        if($search != null) {
            $algoliaClasses = $algoliaClasses->where('name', 'LIKE', '%' . $search . '%');
        }
        
        $algoliaClasses = $algoliaClasses->paginate($entries);
        return $algoliaClasses;
    }

    public function store(StoreAlgoliaClassRequest $request)
    {

        if (is_null($request->id)) {
            $algoliaClass = AlgoliaClass::create([
                "name"  => $request->name,
            ]);

        } else {

            $algoliaClass = AlgoliaClass::findOrFail($request->id);

            $algoliaClass->update([
                "name"  => $request->name,
            ]);

        }

        return response()->json($algoliaClass);
    }

    public function destroy(Request $request)
    {
        AlgoliaClass::where('id',$request->id)->delete();

        return response()->json(true);
    }

    public function all(Request $request) 
    {
        $algoliaClasses = AlgoliaClass::all();

        return response()->json($algoliaClasses);
    }
}

