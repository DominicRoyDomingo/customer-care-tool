<?php

namespace CRM\API\MedicalStuff\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MedicalStuff\MedicalTermType;
use Illuminate\Validation\ValidationException;
use CRM\API\MedicalStuff\MedicalStuffRepository;
use Illuminate\Http\Request;


class TermTypeController extends Controller
{
   private $model;

   protected $termRepo;

   public function __construct(MedicalTermType $model, MedicalStuffRepository $termRepo)
   {
      $this->model = $model;

      $this->termRepo = $termRepo;
   }

   public function index(Request $request)
   {
      $request['locale'] = $request->sortbyLang ?: $request->locale;

      $terms = $this->model
         ->select('medical_term_types.*')
         ->when($request->filter, function ($query) use ($request) {
            $query->where('name', 'LIKE', "%{$request->filter}%");
         })
         ->active()
         ->with(['brands'])
         ->orderBy('name')
         ->get();


      if ($request->sortbyLang) {
         $terms = $this->termRepo->setSortByLang($terms, $request->sortbyLang);
      }

      return response()->json($terms);
   }

   public function store(Request $request)
   {
      $rules = ['name' => 'required'];
      $messages = ['name.required' => 'The :attribute is required.'];
      $request->validate($rules, $messages);

      $termType = "";
      if (is_null($request->id)) {

         $this->termRepo->check_name_duplicate([
            'key' => 'name',
            'value' => $request->name,
            'locale' => $request->locale,
            'brand_id' => $request->brand_id,
         ], $this->model->select('medical_term_types.*')->get(), 'type of terms');

         $termType =  $this->model->create([
            'name' => to_json_add($request->locale, $request->name)
         ]);
      } else {

         $termType =  $this->model->findOrFail($request->id);
         $termType->name = to_json_add($request->locale, $request->name, to_json_remove($request->locale, $termType->name));
         $termType->save();
      }

      return response()->json($termType);
   }

   public function destroy(Request $request)
   {
      $termType = $this->model->findOrFail($request->id);

      if ($termType->is_service || $termType->isBelongToTerms($request->id)) {
         throw ValidationException::withMessages(['name' => strtolower(__('strings.backend.toast.title.in_used_data'))]);
      }

      if ($termType) {
         $this->termRepo->remove_from_brand('brand_term_types', 'term_type_id', $termType->id);
      }

      $termType->delete();

      return response()->json(true);
   }
}
