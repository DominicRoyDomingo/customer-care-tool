<?php

namespace CRM\API\Administrative;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CRM\API\Administrative\AdministrativeRepository;
use Illuminate\Validation\ValidationException;

class AdministrativeController extends Controller
{
   private  $administrativeRepo;

   public function __construct(AdministrativeRepository $administrativeRepo)
   {
      $this->administrativeRepo = $administrativeRepo;
   }

   public function index(Request $request)
   {
      $actions = $this->administrativeRepo->getAll($request);

      return response()->json($actions);
   }

   public function stored(Request $request)
   {
      $request->validate([
         'action' => 'required|min:2'
      ]);

      if (is_null($request->id)) {

         $arrayList =  $this->administrativeRepo->getAll($request);

         $checked =  is_data_exist('action', $request->action, get_data($request->locale, ['action'], $arrayList));

         if ($checked) {
            throw ValidationException::withMessages(['action' => $request->action . ' is an existing Admininstractive action record.']);
         }

         $this->administrativeRepo->create($request->only('action'));
      } else {

         $this->administrativeRepo->update($request->id, $request->only('action', 'id', 'locale'));
      }

      return response()->json(true);
   }

   public function destroy(Request $request)
   {
      $data = $this->administrativeRepo->getById($request->id);

      // if ($category->jobCategoryProfession->count() > 0) {
      // throw ValidationException::withMessages(['job' =>  strtoupper(
      //    $category->category_name
      // ) . '  in used cannot be deleted.']);
      // }

      $data->delete();

      return response()->json(true);
   }
}
