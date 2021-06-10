<?php

namespace CRM\API\Workspace;

use App\Http\Controllers\Controller;
use App\Models\OrganizationUser;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class WorkspaceController extends Controller
{

   public function index(Request $request)
   {
      $search = $request->search;
      $allWorkspaces = $request->all;

      if($allWorkspaces == true) {
         
         // return Organization::all();
      }
      if($search != null) {
         return OrganizationUser::where('user', Auth()->user()->id)->whereHas('organizationModel', function ($query) use ($search){
            $query->where('name', 'like', '%'.$search.'%');
         })->get();
      }
      return response()->json(Auth()->user()->organizationUsers);
   }

   public function all(Request $request)
   {
      $workspaces = Organization::all();

      $workspaces = $workspaces->map(function ($workspace) {
         $workspace->workspaceValid = !Organization::where('id', $workspace->id)->where('main_user', Auth()->user()->id)->get()->isEmpty();
         return $workspace;
      });

      return response()->json($workspaces);
   }

   public function destroy(Request $request)
   {
      $workspace = Organization::where('main_user', Auth()->user()->id)->get();
      // dd();
      if($workspace->count() <= 1) {
         return response()->json(false);
      }
      $workspace = Organization::where('id', $request->id)->first();
      $workspace->delete();
      return response()->json(true);
   }
}
