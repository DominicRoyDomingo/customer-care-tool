<?php

namespace CRM\API\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CRM\API\Platform\PlatformTypeRepository;
use Illuminate\Validation\ValidationException;

class PlatformTypeController extends Controller
{

   protected $platformTypeRepository;

   public function __construct(PlatformTypeRepository $platformTypeRepository)
   {
      $this->platformTypeRepository = $platformTypeRepository;
   }

   public function index(Request $request)
   {
      $data =  $this->platformTypeRepository->getAll($request);

      return response()->json($data);
   }

   public function stored(Request $request)
   {
      $request->validate([
         'name' => 'required'
      ]);

      if (is_null($request->id)) {
         $this->platformTypeRepository->create($request->only('name'));
      } else {
         $this->platformTypeRepository->update($request->id, $request->only('name', 'id'));
      }

      return response()->json(true);
   }
   public function destroy(Request $request)
   {
      $data = $this->platformTypeRepository->getById($request->id);

      $data->delete();

      return response()->json(true);
   }
}
