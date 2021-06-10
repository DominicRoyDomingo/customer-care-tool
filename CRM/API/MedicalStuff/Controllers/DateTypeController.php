<?php

namespace CRM\API\MedicalStuff\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MedicalStuff\TypeDate;
use CRM\API\MedicalStuff\MedicalStuffRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DateTypeController extends Controller
{
    private $model;

    protected $termRepo;

    public function __construct(TypeDate $model, MedicalStuffRepository $termRepo)
    {
        $this->model = $model;

        $this->termRepo = $termRepo;
    }

    public function index(Request $request)
    {
        $result = $this->model
            ->select('type_dates.*')
            ->when($request->filter, function ($query) use ($request) {
                $query
                    ->where('name', 'LIKE', "%{$request->filter}%");
            })
            ->with(['articles'])
            ->get();

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $rules = ['name' => 'required'];
        $messages = ['name.required' => 'The :attribute is required.'];
        $request->validate($rules, $messages);

        $typeDate = '';

        if ($request->action === 'Add') {
            $this->termRepo->check_name_duplicate(
                [
                    'key' => 'name', 'value' => $request->name, 'locale' => $request->locale,
                ],
                $this->model->select('type_dates.*')->get(),
                'type_dates'
            );

            // Insert type date data.
            $typeDate =  $this->model->create(['name' => to_json_add($request->locale, $request->name)]);
        } else {

            // Update type date data.
            $typeDate =  $this->model->findOrFail($request->id);
            $typeDate->name = to_json_add($request->locale, $request->name, to_json_remove($request->locale, $typeDate->name));
            $typeDate->save();
        }


        return response()->json($typeDate);
    }

    public function destroy(Request $request)
    {
        $typeDate = $this->model->findOrFail($request->id);

        if ($typeDate->articles->count() > 0) {
            throw ValidationException::withMessages(['name' => strtolower(__('strings.backend.toast.title.in_used_data'))]);
        }

        $typeDate->delete();

        return response()->json(true);
    }
}
