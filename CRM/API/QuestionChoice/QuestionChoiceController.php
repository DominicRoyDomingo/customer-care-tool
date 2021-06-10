<?php

namespace CRM\API\QuestionChoice;

use App\Http\Controllers\Controller;
use App\Models\Questions\Choice\Choice;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use DB;

class QuestionChoiceController extends Controller
{
    private $model;

    public function __construct(Choice $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        $data = $this->model
            ->select('choices.*')
            ->when($request->filter, function ($query) use ($request){
                $query->where('value', 'LIKE', "%{$request->filter}%");
             })
             ->when($request->sortbyLang, function ($query) use ($request) {
                $query->sortedByNoLang($request->sortbyLang);
             })
            ->orderBy('created_at', 'Desc')
            ->paginate($request->perPage);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $type = $request->type;
        
        Validator::make($request->all(), [
            'form.name' => ['required'],
         ])->validate();
   
         $validateIfUnique = $this->validateIfUnique($request->form['name']);
   
         if ($validateIfUnique) {
            throw ValidationException::withMessages(['unique' =>  $request->form['name'].' is an existing Choice Record']);
         }else{
            switch ($type) {
                case 'create':
                    $choice = $this->model->create([
                        "value" => to_json_add($request->locale, $request->form["name"])
                    ]);
                   break;
                case 'edit':
                    $choice = $this->model->findOrFail($request->form['id']);
                    $choice->value =  to_json_add($request->language,$request->form['name'], to_json_remove($request->language,$choice->value));
                    $choice->save();
                   break;
            }
    
            return response()->json($choice);
         }
    }

    public function validateIfUnique($name){
        $data = DB::table('choices')->where([
           ['value', 'like', "%{$name}%"],
        ])->count();
  
        return ($data > 0) ? true : false;
     }

    public function destroy(Request $request)
    {
        $model = $this->model->findOrFail($request->id);

        $model->delete();

        return response()->json(true);
    }

}
