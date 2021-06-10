<?php



namespace CRM\API\MedicalStuff\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MedicalStuff\TermConnectionDescription;
use App\Models\MedicalStuff\TermDescription;
use CRM\API\MedicalStuff\MedicalStuffRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TermDescriptionController extends Controller
{
   private $model;

   protected $termRepo;

   public function __construct(TermDescription $model, MedicalStuffRepository $termRepo)
   {
      $this->model = $model;

      $this->termRepo = $termRepo;
   }

   public function index(Request $request)
   {
      $results = $this->model
         ->select('connection_descriptions.*')
         ->when($request->filter, function ($query) use ($request) {
            $query
               ->where('name', 'LIKE', "%{$request->filter}%");
         })
         ->get();

      return response()->json($results);
   }

   public function store(Request $request)
   {
      $rules = ['name' => 'required'];
      $messages = ['name.required' => 'The :attribute is required.'];
      $request->validate($rules, $messages);

      $description = '';

      if ($request->action === 'Add') {

         $this->termRepo->check_name_duplicate(
            [
               'key' => 'name', 'value' => $request->name, 'locale' => $request->locale
            ],
            $this->model->select('connection_descriptions.*')->get(),
            'term_descriptions'
         );

         // Insert Post Term Description.
         $description = $this->model
            ->create([
               'name' => to_json_add($request->locale, $request->name),
               'with_value' => (int)$request->with_value ?? 0
            ]);
      } else {

         // Update Post Term Description.
         $description = $this->model->findOrFail($request->id);
         $description->name = to_json_add($request->locale, $request->name, to_json_remove($request->locale, $description->name));
         $description->with_value = (int)$request->with_value ?? 0;
         $description->save();
      }

      return response()->json($description);
   }

   public function destroy(Request $request)
   {
      $description = $this->model->findOrFail($request->id);

      if ($description->term_connections_descriptions) {
         throw ValidationException::withMessages(['name' => strtolower(__('strings.backend.toast.title.in_used_data'))]);
      }

      $description->delete();

      return response()->json(true);
   }

   public function get_term_connection_descriptions(Request $request)
   {
      $descriptions = $this->termRepo->linked_term_description($request->parent_id, $request->child_id);

      return response()->json($descriptions);
   }

   public function store_term_connection_description(Request $request)
   {
      // $rules = ['description' => 'required'];
      // $messages = ['description.required' => 'The :attribute is required.'];
      // $request->validate($rules, $messages);

      $resp = '';
      $lang = $request->locale;
      $note = strlen($request->note) > 0 ? to_json_add($lang, $request->note) : null;
      $parentTerm = $this->termRepo->findById($request->parent_id);
      $termIdArray = json_decode($parentTerm->medical_terms) ?? [];
      $request['is_mutual'] = $request->method === 'mutual' ?: false;

      if (!in_array($request->child_id, $termIdArray)) {
         $request['id']  = $request->parent_id;
         $request['key'] = 'link';
         $resp = $this->termRepo->link_term($request);
      }

      if ($request->action === 'Add') {

         $this->check_term_notes($request->parent_id, $request->child_id,  $note);

         $data = [
            'term_id' => $request->parent_id,
            'linked_term_id' => $request->child_id,
            'description_id' => $request->description,
            'value' => $request->value,
            'note' => $note,
            'method' => $request->method ?: 'none'
         ];

         $resp = TermConnectionDescription::create($data);
      } else {

         // Update Post Term Description.
         $resp = TermConnectionDescription::findOrFail($request->id);


         $note = null;
         if (strlen($request->note) > 0) {
            $note = to_json_add($lang, $request->note, to_json_remove($lang, $resp->note));
         } elseif (strlen($request->note) === 0 && $resp->note) {
            $arrs = to_json_remove($lang, $resp->note);
            $note = !empty($arrs) ? json_encode($arrs) : null;
         }

         $resp->note = $note;
         $resp->value = $request->value;
         $resp->description_id = $request->description;
         $resp->method = $request->method ?: 'none';

         $resp->save();

         if (!$request->is_mutual) {
            TermConnectionDescription::where('term_id', $resp->linked_term_id)
               ->where('linked_term_id', $resp->term_id)
               ->delete();
         }
      }

      $connection = TermConnectionDescription::with(['term_description'])
         ->find($resp->id);

      return response()->json($connection);
   }

   public function check_term_notes($termId, $linkedTermId, $notes): void
   {
      $tcd1 = TermConnectionDescription::where('term_id', $termId)
         ->where('linked_term_id', $linkedTermId)
         ->first();

      $tcd2 = TermConnectionDescription::where('term_id', $linkedTermId)
         ->where('linked_term_id', $termId)
         ->first();

      if ($tcd1 &&  $tcd2 && $tcd1->description_id ===  $tcd2->description_id) {
         if (in_array($notes, string_to_array($tcd1->note)) || in_array($notes, string_to_array($tcd2->note))) {
            throw ValidationException::withMessages(['notes' => __('validation.exist', ['attribute' => $notes, 'data' => __('menus.backend.sidebar.descriptions')])]);
            return;
         }
      }
   }

   public function delete_term_descriptions(Request $request)
   {
      $termConDesc = TermConnectionDescription::findOrFail($request->id);

      $termConDesc->delete();

      return response()->json(true);
   }
}
