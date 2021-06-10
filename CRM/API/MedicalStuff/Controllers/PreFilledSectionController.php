<?php

namespace CRM\API\MedicalStuff\Controllers;

use Facade\CodeEditor\Http\Controllers\Controller;
use App\Models\MedicalStuff\PreFilledSection;
use Illuminate\Http\Request;
use CRM\API\MedicalStuff\MedicalStuffRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use DB;

// use CRM\API\MedicalStuff\Traits\MedicalStuffTrait;

class PreFilledSectionController extends Controller
{
   // use MedicalStuffTrait;

   private $model;

   protected $termRepo;

   public function __construct(PreFilledSection $model, MedicalStuffRepository $termRepo)
   {
      $this->model = $model;

      $this->termRepo = $termRepo;
   }

   public function index(Request $request)
   { 
      $sections = $this->model
      ->select('pre_filled_section.*')
      ->when($request->filter, function ($query) use ($request){
         $query->where('name', 'LIKE', "%{$request->filter}%");
      })
      ->when($request->sortbyLang, function ($query) use ($request) {
         $query->sortedByNoLang($request->sortbyLang);
      })
      ->with([
         'item_type',
      ])
      ->orderBy('created_at', 'Desc')
      ->paginate($request->perPage);
      
      return response()->json($sections);
   }

   public function store(Request $request)
   {
      Validator::make($request->all(), [
         'form.section_name' => ['required'],
         'form.section_priority' => ['required'],
         'form.section_type' => ['required'],
      ])->validate();

      $validateIfUnique = $this->validateIfUnique($request->form['section_name'], $request->form['section_priority'], $request->form['section_type']);

      if ($validateIfUnique) {
         throw ValidationException::withMessages(['unique' => $request->form['section_name'].' is an existing Pre Filled Section Record']);
      }else{
         $response = $this->model->create([
            'name' => to_json_add($request->language,$request->form['section_name']),
            'tag_used_id' =>  $request->form['section_priority'],
            'type_of_publishing_item_id' => $request->form['section_type'],
         ]);
         return response()->json($response);
      }
   }

   public function update(Request $request)
   {
      Validator::make($request->all(), [
         'form.section_name' => ['required'],
         'form.section_priority' => ['required'],
         'form.section_type' => 'required',
      ])->validate();

      $validateIfUnique = $this->validateIfUnique($request->form['section_name'], $request->form['section_priority'], $request->form['section_type']);

      if ($validateIfUnique) {
         throw ValidationException::withMessages(['unique' => 'This value is incorrect']);
      }else{
         $section = $this->model->findOrFail($request->form['id']);
         $section->name = to_json_add($request->language,$request->form['section_name'], to_json_remove($request->language,$section->name));
         $section->tag_used_id = $request->form['section_priority'];
         $section->type_of_publishing_item_id = $request->form['section_type'];
         $section->save();

         return response()->json(true);
      }
   }

   public function validateIfUnique($name, $tag, $type){
      $section = DB::table('pre_filled_section')->where([
         ['name', 'like', "%{$name}%"],
         ['tag_used_id', $tag],
         ['type_of_publishing_item_id', $type],
      ])->count();

      return ($section > 0) ? true : false;
   }

   public function retrieve($id, Request $request)
   {  
      $data = $this->model
      ->where('Type_of_publishing_item_id', '=', $id)
      ->get();

      return response()->json($data);
   }

   public function destroy(Request $request)
   {
      $resp = $this->model->findOrFail($request->id)->delete();

      return response()->json($resp);
   }

}
