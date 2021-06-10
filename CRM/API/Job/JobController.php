<?php

namespace CRM\API\Job;

use App\Http\Controllers\Controller;
use App\Models\Jobs\JobDescription;
use App\Models\Jobs\JobProfession;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{

   private $jobRepository;

   public function __construct(JobRepository $jobRepository)
   {
      $this->jobRepository = $jobRepository;
   }

   public function _index(Request $request)
   {
      $categories = $this->jobRepository->getAll($request);

      return response()->json($categories);
   }

   public function _get_categories(Request $request)
   {
      $categories = $this->jobRepository->getCategories($request);

      return response()->json($categories);
   }

   public function _get_filtered(Request $request)
   {
      $dataset = $this->jobRepository->getFiltered($request);

      return response()->json($dataset);
   }

   public function _get_filtered_job_professions(Request $request)
   {
      $dataset = $this->jobRepository->getFilteredJobProfession($request);

      return response()->json($dataset);
   }

   // for Category here
   public function _store_category(Request $request)
   {
      $id = $request->id != null ? "," . $request->id : "";
      $request->validate(
         [
            'category' => "required|unique:job_category,category->en" . $id . "|unique:job_category,category->it" . $id . "|unique:job_category,category->de" . $id . "|unique:job_category,category->ph-fil" . $id,
         ],
         [
            // 'category.unique' => $request->category . ' is an existing specialization category record',

            'category.unique' =>  __('validation.exist', ['attribute' => $request->category, 'data' => __('menus.backend.sidebar.category')]),

         ]
      );

      if (is_null($request->id)) {

         $this->jobRepository->create($request->only('category', 'brand_id'));
      } else {

         $this->jobRepository->update($request->id, $request->only('category', 'id', 'locale'));
      }

      return response()->json(true);
   }

   public function _destroy_category(Request $request)
   {
      $category = $this->jobRepository->getById($request->id);

      if ($category->jobCategoryProfession->count() > 0) {
         throw ValidationException::withMessages(['job' =>  strtoupper(
            $category->category_name
         ) . '  in used cannot be deleted.']);
      }

      $category->delete();

      return response()->json(true);
   }

   // for profession here
   public function _store_profession(Request $request)
   {
      $id = $request->id != null ? "," . $request->id : "";
      $resp = '';

      $request->validate(
         [
            'profession' => "required|unique:job_profession,profession->en" . $id . "|unique:job_profession,profession->it" . $id . "|unique:job_profession,profession->de" . $id . "|unique:job_profession,profession->ph-fil" . $id,
            'category' => 'required|array'
         ],
         [
            'profession.unique' => $request->profession . ' is an existing profession record',
         ]
      );

      if (is_null($request->id)) {

         $this->jobRepository->create_profession($request->only('profession', 'category', 'locale'));
      } else {
         $this->jobRepository->update_profession($request->id, $request->only('profession', 'category', 'locale'));
      }

      return response()->json(true);
   }


   public function _destroy_profession(Request $request)
   {
      $jp = JobProfession::findOrFail($request->id);

      if ($jp->jobDescriptions->count() > 0) {
         throw ValidationException::withMessages(['profession' =>  strtoupper(
            $jp->profession_name
         ) . '  in used cannot be deleted.']);
      }

      $jp->delete();

      return response()->json(true);
   }

   // for Description Here
   public function _store_description(Request $request)
   {
      $id = $request->id != null ? "," . $request->id : "";
      $resp  = '';

      $request->validate(
         [
            'profession'   => 'required',
            'description'  => "required|min:2|unique:job_description,description->en" . $id . "|unique:job_description,description->it" . $id . "|unique:job_description,description->de" . $id . "|unique:job_description,description->ph-fil" . $id,
         ],
         [
            'description.required' => 'The specialization field is required.',
            'description.unique' => $request->description . ' is an existing specialization record',
         ]
      );

      if (is_null($request->id)) {
         $resp  =  $this->jobRepository->create_description($request->only('profession', 'locale', 'description'));
      } else {
         $resp  =  $this->jobRepository->update_description($request->id, $request->only('profession', 'locale', 'description'));
      }

      return response()->json($resp);
   }

   public function _destroy_description(Request $request)
   {
      $jp = JobDescription::findOrFail($request->id);

      $jp->delete();

      return response()->json(true);
   }
}
