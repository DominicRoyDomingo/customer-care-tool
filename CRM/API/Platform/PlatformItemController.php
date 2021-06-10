<?php

namespace CRM\API\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CRM\API\Platform\PlatformItemRepository;
use Illuminate\Validation\ValidationException;

class PlatformItemController extends Controller
{

   protected $platformItemRepository;

   public function __construct(PlatformItemRepository $platformItemRepository)
   {
      $this->platformItemRepository = $platformItemRepository;
   }

   public function index(Request $request)
   {
      $data =  $this->platformItemRepository->getAll($request);

      return response()->json($data);
   }

   public function store(Request $request)
   {
      $request->validate([
         'brand_id' => 'required',
         'platform_type_id' => 'required'
      ]);

      if (is_null($request->id)) {
         $this->platformItemRepository->create($request->only('brand_id','platform_type_id'));
      } else {
         $this->platformItemRepository->update($request->id, $request->only('brand_id','platform_type_id', 'id'));
      }

      return response()->json(true);
   }
   public function destroy(Request $request)
   {
      $data = $this->platformItemRepository->getById($request->id);

      $data->delete();

      return response()->json(true);
   }
}
