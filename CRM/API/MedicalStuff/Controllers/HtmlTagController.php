<?php

namespace CRM\API\MedicalStuff\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MedicalStuff\HtmlTag;
use CRM\API\MedicalStuff\MedicalStuffRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Backend\HtmlTagPriority\StoreHtmlTagPriorityRequest;

class HtmlTagController extends Controller
{
    private $model;

    protected $termRepo;

    public function __construct(HtmlTag $model, MedicalStuffRepository $termRepo)
    {
        $this->model = $model;

        $this->termRepo = $termRepo;
    }

    public function index(Request $request)
    {
        $htmlTags = $this->model->select('html_tags.*')
            ->when($request->filter, function ($query) use ($request) {
                $query
                    ->where('description', 'LIKE', "%{$request->filter}%");
            })
            ->get();
            
        return response()->json($htmlTags);
    }

    public function store(StoreHtmlTagPriorityRequest $request)
    {
        if ($request->action === 'Add') {
            // Insert type date data.
            $htmlTag =  $this->model->create(['description' => $request->description]);
        } else {

            // Update type date data.
            $htmlTag =  $this->model->findOrFail($request->id);
            $htmlTag->description = $request->description;
            $htmlTag->save();
        }


        return response()->json($htmlTag);
    }

    public function destroy(Request $request)
    {
        $htmlTag = $this->model->findOrFail($request->id);

        $htmlTag->delete();

        return response()->json(true);
    }
}
