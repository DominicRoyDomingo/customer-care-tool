<?php

namespace CRM\API\Organization;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Organization;
use App\Helpers\General\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class OrganizationController extends Controller
{

   public function index(Request $request)
   {
      $lang = \Lang::getLocale();

      $organizationCategories = Organization::all()->map(function ($org) use($lang){

         $org['category'] = getTranslation($org['category'], $lang);
     
         return $org;
     
      });

      return response()->json($organizationCategories);
   }

//    public function show(Request $request, $id)
//    {
//       $brand = $this->brandRepository->getById($id);
      
//       return response()->json($brand);
//    }

   // for Category here
   public function store(Request $request)
   {
        $lang = \Lang::getLocale();

        if (is_null($request->id)) {

            $request->validate([
                    "org_name"      => "required",
                    "logo"         => "nullable",
                    "isDefault"     => "required",
                ],
                [
                    'org_name.required'  => 'Category is required.',
                ]
            );

            $organization = Organization::create([
                "name"          => $request->org_name,
                "main_user"     => auth()->user()->id,
                "isDefault"     => $request->isDefault == null ? 0 : $request->isDefault,
            ]);
            
            if($request->logo != null) {
                $logo = $request->file('logo');

                $this->uploadImage($organization, $logo);
            }
            

            auth()->user()->organizationUsers()->create([
                "organization"  => $organization->id
            ]); 

        }
      
            // dd($organizationCategory);
        return response()->json($organization);
    }

    public function uploadImage($organization, $logo)
    {
        $logo = new ImageHelper([
            'driver' => 's3',
            'id' => $organization->id,
            's3_storage_path' => 'med4care-customercare',
            's3_folder_path' => 'provider-images/',
            'file' => $logo,
        ]);

        $logo->deleteDirectory();

        if ($logo->upload()) {
            $organization->logo =  $logo->imageUrl();
            $organization->save();
        }
    }

//    public function destroy(Request $request)
//    {
//       OrganizationCategory::where('id',$request->id)->delete();

//       return response()->json(true);
//    }

//    public function getOrganizationCategoryName( Request $request ) {
//       // dd($request->id());
//       $organizationCategory = OrganizationCategory::whereId( $request->id ) -> first();
//       // dd($organizationCategory);
//       $message = ucwords( string_to_value( $request->lang , $organizationCategory->category) );
//       // dd($message);
//       return response() -> json($message);

//   }

//    public function uploadImage($organization, $file)
//    {
//       $image = new ImageHelper([
//          'driver' => 's3',
//          'id' => $organization->id,
//          's3_storage_path' => 'med4care-customercare',
//          's3_folder_path' => 'icons/',
//          'file' => $file,
//       ]);

//       $image->deleteDirectory();

//       if ($image->upload()) {
//          $organization->icon =  $image->imageUrl();
//          $organization->save();
//       }
//    }
}
