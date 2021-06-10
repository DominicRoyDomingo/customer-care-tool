<?php

namespace CRM\API\MedicalStuff\Controllers;

use App\Events\Backend\Terms\LinkedBrandEvent;
use App\Models\Jobs\JobDescription;
use App\Models\ArticleNotes;
use App\Models\MedicalStuff\MedicalTerm;
use CRM\API\Job\JobRepository;
use Facade\CodeEditor\Http\Controllers\Controller;
use App\Http\Requests\Backend\MedicalStuff\Term\StoreTermIconRequest;
use App\Models\MedicalStuff\MedicalArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Auth;
use CRM\API\MedicalStuff\MedicalStuffRepository;
use CRM\API\MedicalStuff\Requests\StoreTermRequest;
use CRM\API\MedicalStuff\Rules\ValidateIcon;

// use CRM\API\MedicalStuff\Traits\MedicalStuffTrait;

class TermController extends Controller
{
   // use MedicalStuffTrait;

   protected $termRepo;

   protected $jobRepo;

   public function __construct(MedicalStuffRepository $termRepo, JobRepository $jobRepo)
   {
      $this->termRepo = $termRepo;

      $this->jobRepo = $jobRepo;
   }


   // Start Terms API response =====>
   public function index(Request $request)
   {
      if ($request->groupByTermType) {

         $terms = $this->termRepo->setGroupByTermType($request);
         return response()->json($terms);
      }

      if ($request->filterByProviderType || $request->filterByService) {

         $terms = $this->termRepo->getProviderTypeOrService($this->termRepo->getAll($request), $request);
         return response()->json($terms);
      }

      $terms = $this->termRepo->getActivePaginated($request->perPage);

      return response()->json($terms);
   }

   public function selectActiveTerms(Request $request)
   {
      $terms = $this->termRepo->get_active_terms();

      return response()->json($terms);
   }

   public function post(StoreTermRequest $request)
   {
      $resp = (object) '';
      $inputs = $request->only('name', 'brand_id', 'specializations', 'term_types');

      if ($request->action === 'Add') {

         $data = array(
            'key' => 'name',
            'value' => $request->name,
            'locale' => $request->locale,
            'brand_id' => $request->brand_id,
         );
         $this->termRepo->check_name_duplicate($data, $this->termRepo->get(), 'terms');

         $resp = $this->termRepo->create($inputs);
      } else {
         $resp = $this->termRepo->update($request->id, $inputs);
      }

      return response()->json($resp);
   }

   public function show(Request $request)
   {
      $term = $this->termRepo->get_active_term($request->id);

      if (!$term) {
         throw ValidationException::withMessages(['name' => "Model not found"]);
         return;
      }

      return response()->json($term);
   }

   public function get_provider_types(Request $request)
   {
      $terms = $this->termRepo->getProviderTypeOrService($this->termRepo->getAll($request), $request);

      return response()->json($terms);
   }

   public function get_linked_terms(Request $request)
   {
      $linkedTerms = $this->termRepo->linked_terms($request);

      return response()->json($linkedTerms);
   }

   public function get_linked_terms_actor(Request $request)
   {
      $linkedTerms = $this->termRepo->linked_terms_actor($request);

      return response()->json($linkedTerms);
   }

   public function get_linked_terms_details(Request $request)
   {
      $linkedTermsDetails = $this->termRepo->linked_terms_details($request);

      return response()->json($linkedTermsDetails);
   }

   public function get_linked_provider_terms(Request $request)
   {
      $linkedProviderTerms = $this->termRepo->get_provider_types();

      return response()->json($linkedProviderTerms);
   }

   public function get_professional_terms(Request $request)
   {

      $professionalTerms = $this->termRepo->get_professional_terms($request);

      return response()->json($professionalTerms);
   }

   public function get_category_services(Request $request)
   {
      $categoryServices = $this->termRepo->get_category_services();

      return response()->json($categoryServices);
   }

   public function get_services_by_category_service($categoryServiceId)
   {

      $services = $this->termRepo->get_services_by_category_service($this->termRepo->getById($categoryServiceId));

      return response()->json($services);
   }

   public function get_services_terms(Request $request)
   {
      $servicesTerms = $this->termRepo->get_paginated_services_terms($request);

      return response()->json($servicesTerms);
   }

   public function get_linked_term_id(Request $request)
   {
      $idArray = json_decode($request->medical_terms) ?? [];

      return response()->json($idArray);
   }

   public function destroy(Request $request)
   {
      $resp = $this->termRepo->remove($request);

      return response()->json($resp);
   }

   public function post_link_term(Request $request)
   {
      $childTerm = $this->termRepo->findById($request->child_id);

      if (!$childTerm->has_term_types) {
         throw ValidationException::withMessages(['name' => "No Term Types"]);
      }

      $resp = $this->termRepo->link_term($request);

      return response()->json($resp);
   }

   public function getNameSpecialization($id, $lang)
   {
      $get_name = JobDescription::whereId($id)->first();

      $name = ucwords(string_to_value($lang, $get_name->description));

      return $name;
   }
   // end term type API response =====>

   public function link_to_brand(Request $request)
   {
      $rules = ['brand_name' => 'required'];
      $messages = ['brand_name.required' => 'The :attribute is required.'];

      $request->validate($rules, $messages);

      $tableName  = 'brand_terms';
      $tableField = 'terminology_id';

      if ($request->linkedType === 'term_type') {
         $tableName  = "brand_term_types";
         $tableField  = "term_type_id";
      } else if ($request->linkedType === 'article') {
         $tableName  = "brand_articles";
         $tableField  = "article_id";
      }

      $data = [
         'table_name' => $tableName,
         'brand_id' => $request->brand_id,
         'field_key' => $tableField,
         'brands' => $request->brand_name,
         'value' => $request->id,
         'link' => false,
      ];
      // dd($data);
      // linked to brand event
      LinkedBrandEvent::dispatch($data);

      return response()->json(true);
   }

   public function get_notes(Request $request)
   {
      $notes = ArticleNotes::leftJoin('medical_articles as ma', 'ma.id', 'article_notes.article')
         ->select([
            'ma.title', 'article_notes.id', 'article_notes.article', 'article_notes.notes', 'article_notes.notes_date', 'article_notes.created_by', 'article_notes.created_at'
         ])
         ->where('article', '=', $request->id)
         ->get();
      $dataArray = [];
      foreach ($notes as $note) {
         $created_by = DB::table('users')->where('id', '=', $note->created_by)->select(['first_name', 'last_name'])->first();
         $dataArray[] = [
            'id' => $note->id,
            'article_title' => getTranslation($note->title, $request->lang),
            'article_id' => $note->article,
            'notes' => $note->notes,
            'notes_date' => $note->notes_date,
            'created_by' => $created_by->first_name . ' ' . $created_by->last_name,
            'created_at' => date('j F Y', strtotime($note->created_at))
         ];
      }
      return response()->json($dataArray);
   }

   public function post_notes(Request $request)
   {
      $article_id = $request->article_id;
      ArticleNotes::where('article', '=', $article_id)->delete();
      $array = [];
      foreach ($request->notes as $note) {
         $notes = new ArticleNotes;
         $notes->article = $article_id;
         $notes->notes = $note['notes'];
         $notes->notes_date = (isset($note['date_requested']) ? $note['date_requested'] : $note['notes_date']);
         $notes->created_by = Auth::user()->id;
         $notes->save();
      }
      return response()->json(true);
   }

   public function post_icon(StoreTermIconRequest $request)
   {
      $this->termRepo->store_icon($request);

      return response()->json(true);
   }

   public function show_icons(MedicalTerm $icon)
   {
      return response()->json($icon->term_icons);
   }

   public function destroy_icon(Request $request)
   {
      $resp = $this->termRepo->remove_icon($request);

      return response()->json($resp);
   }


   // TYPE OF AUTHOR
   public function get_type_author(Request $request)
   {
      $lang = $request->locale;
      $keyword = $request->filter;
      $author_type_connection = DB::table('author_types')->select(['id', 'name', 'created_at', 'updated_at'])
         ->when($keyword, function ($query) use ($keyword) {
            if (!empty($keyword)) {
               $new_like_query = '
               (
                  ( name LIKE "%' . ucwords($keyword) . '%" )
               )
            ';
               return $query->whereRaw(" ( " . $new_like_query . ")");
            }
         })
         ->get();
      $language = 'English';
      if ($lang == 'it') {
         $language = 'Italian';
      }
      if ($lang == 'de') {
         $language = 'German';
      }
      if ($lang == 'ph-fil') {
         $language = 'Filipino';
      }
      if ($lang == 'ph-bis') {
         $language = 'Visayan';
      }
      $authorTypeArray = array();
      foreach ($author_type_connection as $datas) {
         $name = $this->getNameAuthorType($datas->id, $lang);
         $base_name = (!empty($name) ? $name : $this->getNameAuthorType($datas->id, 'en'));
         if ($name == '' && $base_name == '') {
            $base_name_italy = $this->getNameAuthorType($datas->id, 'it');
            if ($base_name_italy !== '') {
               $base_name = $base_name_italy;
            } else {
               $base_name_germany = $this->getNameAuthorType($datas->id, 'de');
               if ($base_name_germany !== '') {
                  $base_name = $base_name_germany;
               } else {
                  $base_name_filipino = $this->getNameAuthorType($datas->id, 'ph-fil');
                  if ($base_name_filipino !== '') {
                     $base_name = $base_name_filipino;
                  } else {
                     $base_name = $this->getNameAuthorType($datas->id, 'ph-bis');
                  }
               }
            }
         }
         array_push($authorTypeArray, [
            'id' => $datas->id,
            'name' => $base_name,
            'created_at' => date('d/m/Y', strtotime($datas->created_at)),
            'updated_at' => date('d/m/Y', strtotime($datas->updated_at)),
            'convertion' => (!empty($name) ? false : true),
            'language' => $language
         ]);
      }
      return response()->json($authorTypeArray);
   }

   public function getNameAuthorType($id, $lang)
   {
      $author_type = DB::table('author_types')->where('id', '=', $id)->first();
      $name = ucwords(string_to_value($lang, $author_type->name));
      return $name;
   }

   public function postTypeAuthor(Request $request)
   {
      try {
         $lang = $request->locale;
         $request_name = $request->name;
         $check = DB::table('author_types')->get();
         foreach ($check as $req_type) {
            $x0 = 'en';
            $x1 = 'it';
            $x2 = 'de';
            $x3 = 'ph-fil';
            $x4 = 'ph-bis';
            for ($x = 0; $x < 5; $x++) {
               $languange = $x + $x;
               $get_name = getTranslation($req_type->name, $lang);
               if (strtolower($get_name) == strtolower($request_name) && $req_type->id != $request->id) {
                  return response()->json(['duplicate' => true, 'name' => $request_name]);
               }
            }
         }
         if (isset($request->id) || !empty($request->id)) {
            $authorType = DB::table('author_types')->where('id', $request->id)->first();
            $authorTypeVal = string_add_json($lang, ucwords($request->name), string_remove($lang, $authorType->name));
            DB::table('author_types')
               ->where('id', $request->id)
               ->update(['name' => $authorTypeVal]);
         } else {
            DB::table('author_types')->insert([
               'name' => json_encode([$lang => $request_name])
            ]);
         }
         return response()->json(['responseStatus' => true]);
      } catch (\Exception $e) {
         dd($e->getMessage());
      }
   }

   public function get_type_author_name(Request $request)
   {
      $id = $request->id;
      $lang = $request->lang;
      $request_type = DB::table('author_types')->whereId($id)->first();
      $name = ucwords(string_to_value($lang, $request_type->name));
      return $name;
   }

   public function delete_type_author(Request $request)
   {
      $get_reason = DB::table('article_authors')->where('author_type', '=', $request->id);
      if ($get_reason->count() > 0) {
         $name = $get_reason->first()->name;
         return response()->json(['inUse' => true, 'name' => ucwords(string_to_value($request->lang, $name))]);
      }
      DB::table('author_types')->whereId($request->id)->delete();
      return response()->json(true);
   }


   // ==================================================== 
   public function store_term_note(Request $request)
   {
      $locale = request()->locale;
      $note = request()->note;
      $term = $this->termRepo->getById($request->id);

      if ($request->action === 'add') {
         $term->note = to_json_add($locale, $note);
      } else {
         $term->note = to_json_add($locale, $note, to_json_remove($locale, $term->note));
      }

      $term->save();

      $this->termRepo->update_provider_sync_reference($term);

      return response()->json($term);
   }

   // ============ for linked corses ============

   public function get_course_term_id(Request $request)
   {
      $data = DB::table($request->type)
         ->where('article_id', $request->article_id)
         ->get();

      $idArray = [];

      foreach ($data as $value) {
         $idArray[] = $value->term_id;
      }

      return response()->json($idArray);
   }


   public function get_terms_by_type(Request $request)
   {
      $terms = $this->termRepo->model()
         ->select('medical_terms.*')
         ->active()
         ->when($request->filter, function ($query) {
            $query->filtered();
         })
         ->when($request->type, function ($query) use ($request) {
            $query->filterByTermTypes($request->type);
         })
         ->orderByLinkCourseTermType($request->article_id)
         ->paginate($request->perPage);

      return response()->json($terms);
   }

   public function link_course_term(Request $request)
   {
      $query = DB::table($request->type);

      if ($request->key === 'link') {
         $query
            ->insert([
               'term_id' => $request->term_id,
               'article_id' => $request->article_id,
            ]);
      } else {
         $query
            ->where('term_id', $request->term_id)
            ->where('article_id', $request->article_id)
            ->delete();
      }

      $this->termRepo->update_course_sync_reference(MedicalArticle::find($request->article_id));

      return response()->json(true);
   }
}
