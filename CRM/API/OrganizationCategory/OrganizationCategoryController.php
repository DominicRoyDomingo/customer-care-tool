<?php

namespace CRM\API\OrganizationCategory;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\OrganizationCategory;
use App\Helpers\General\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class OrganizationCategoryController extends Controller
{

   public function index(Request $request)
   {
      $lang = \Lang::getLocale();

      $organizationCategories = OrganizationCategory::all()->map(function ($org) use($lang){

         $org['category'] = getTranslation($org['category'], $lang);
     
         return $org;
     
      });

      return response()->json($organizationCategories);
   }

   public function show(Request $request, $id)
   {
      $brand = $this->brandRepository->getById($id);
      
      return response()->json($brand);
   }

   // for Category here
   public function store(Request $request)
   {
      $lang = \Lang::getLocale();
      // dd($request->all());
      if (is_null($request->id)) {

         $request->validate([
               "icon"      => "required",
               "category"  => "required",
            ],
            [
               'category.required'  => 'Category is required.',
               'icon.required'      => 'Icon is required.',
            ]
         );

         $categoryName = ucwords($request->category);

         $icon = $request->file('icon');

         $organizationCategory = OrganizationCategory::create([
            "category"      => json_encode([
                        $lang => $categoryName,
                        ]),
         ]);
         // dd($organizationCategory)
         $icon = $this->uploadImage($organizationCategory, $icon);

         $organizationCategory->category = getTranslation($organizationCategory->category, $lang);

      } else {
         $request->validate([
               "icon"      => "required",
               "category"  => "required",
            ],
            [
               'category.required'  => 'Category is required.',
               'icon.required'      => 'Icon is required.',
            ]
         );

         $categoryName = ucwords($request->category);

         $organizationCategory = OrganizationCategory::findOrFail($request->id);

         $icon = $request->file('icon');

         $organizationCategory->update([
            "category"      => json_encode([
                        $lang => $categoryName,
                        ]),
         ]);

         $icon = $this->uploadImage($organizationCategory, $icon);

         $organizationCategory->category = getTranslation($organizationCategory->category, $lang);
      }
      
            // dd($organizationCategory);
      return response()->json($organizationCategory);
      // return response()->json($request->all());

      // if (is_null($request->id)) {
      //    $sameBrand = Brand::where('name', $request->name)->first();

      //    if ($sameBrand != null) {
      //       throw ValidationException::withMessages(['request' => $request->name . ' already exists']);
      //    }

      //    $this->brandRepository->create($request->only('name', 'file', 'website'));
      // } else {

      //    $this->brandRepository->update($request->id, $request->only('category', 'name', 'logo', 'website'));
      // }

      // return response()->json(true);
   }

   public function destroy(Request $request)
   {
      OrganizationCategory::where('id',$request->id)->delete();

      return response()->json(true);
   }

   public function getOrganizationCategoryName( Request $request ) {
      // dd($request->id());
      $organizationCategory = OrganizationCategory::whereId( $request->id ) -> first();
      // dd($organizationCategory);
      $message = ucwords( string_to_value( $request->lang , $organizationCategory->category) );
      // dd($message);
      return response() -> json($message);

  }

   public function uploadImage($organization, $file)
   {
      $image = new ImageHelper([
         'driver' => 's3',
         'id' => $organization->id,
         's3_storage_path' => 'med4care-customercare',
         's3_folder_path' => 'icons/',
         'file' => $file,
      ]);

      $image->deleteDirectory();

      if ($image->upload()) {
         $organization->icon =  $image->imageUrl();
         $organization->save();
      }
   }
}
