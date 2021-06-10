<?php

namespace CRM\API\Engagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CRM\API\Engagement\EngagementRepository;
use Illuminate\Validation\ValidationException;

class EngagementController extends Controller
{

   protected $engagementRepository;

   public function __construct(EngagementRepository $engagementRepository)
   {
      $this->engagementRepository = $engagementRepository;
   }

   public function index(Request $request)
   {
      $data =  $this->engagementRepository->getAll($request);

      return response()->json($data);
   }

   public function stored(Request $request)
   {
      $request->validate([
         'engagement' => 'required|min:2',
         'brands' => 'required'
      ]);

      $engagement = null;

      if (is_null($request->id)) {
         $arrayList =  $this->engagementRepository->getAll($request);

         $checked =  is_data_exist('engagement', $request->engagement, get_data($request->locale, ['engagement'], $arrayList));

         if ($checked) {
            throw ValidationException::withMessages(['engagement' => $request->engagement . ' is an existing engagement record.']);
         }

         $engagement = $this->engagementRepository->create($request->only('engagement', 'brands'));
      } else {
         $engagement = $this->engagementRepository->update($request->id, $request->only('engagement', 'brands', 'id', 'locale'));
      }

      $engagement->responseStatus = true;

      return response()->json($engagement);
   }

   public function destroy(Request $request)
   {
      $data = $this->engagementRepository->getById($request->id);

      // if ($category->jobCategoryProfession->count() > 0) {
      // throw ValidationException::withMessages(['job' =>  strtoupper(
      //    $category->category_name
      // ) . '  in used cannot be deleted.']);
      // }

      $data->delete();

      return response()->json(true);
   }
}
