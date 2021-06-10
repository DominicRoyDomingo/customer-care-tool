<?php

namespace CRM\API\Brand;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
   private $brandRepository;

   public function __construct(BrandRepository $brandRepository)
   {
      $this->brandRepository = $brandRepository;
   }

   public function index(Request $request)
   {
      $brands = $this->brandRepository->getAll($request);

      return response()->json($brands);
   }

   public function show(Request $request, $id)
   {
      $brand = $this->brandRepository->getById($id);

      return response()->json($brand);
   }

   // for Category here
   public function store(Request $request)
   {
      $brand = null;

      if (is_null($request->id)) {
         $request->validate(
            [
               'name'         => 'required|min:2|unique:brands',
               'categories'   => 'required',
            ],
            [
               'name.required' => 'Brand name is required',
               'name.unique' => $request->name . ' is an existing brand record',
               'categories.required' => 'Categories is required'
            ]
         );
         $brand = $this->brandRepository->create($request->only('name', 'file', 'website', 'isDefault', 'org_id', 'categories'));
      } else {
         $request->validate([
            'name'         => 'required|min:2',
            'categories'   => 'required'
         ]);
         
         $brand = $this->brandRepository->update($request->id, $request->only('name', 'file', 'website', 'isDefault', 'org_id', 'categories'));
      }

      $brand->responseStatus = true;

      return response()->json($brand);
   }

   public function destroy(Request $request)
   {
      $brand = $this->brandRepository->getById($request->id);

      if ($brand->brand_clients->count() > 0) {
         throw ValidationException::withMessages(['brand' =>  strtoupper(
            $brand->name
         ) . ' is used and cannot be deleted.']);
      }

      if ($brand->brand_engagements > 0) {
         throw ValidationException::withMessages(['brand' =>  strtoupper(
            $brand->name
         ) . ' is used and cannot be deleted.']);
      }

      if ($brand->brand_platforms->count() > 0) {
         throw ValidationException::withMessages(['brand' =>  strtoupper(
            $brand->name
         ) . ' is used and cannot be deleted.']);
      }

      $brand->delete();

      Storage::disk('s3')->deleteDirectory(config('access.s3_path.brands') . '/' . $brand->id);

      return response()->json(true);
   }

   public function getCategories(Request $request)
   {
      $lang = \Lang::getLocale();

      $categories = Category::select('id', 'category_name')->get();

      $categoriesFinal = $categories->map(function ($categories) use ($lang) {
         // dd($categories->category_name);
         $categories->category_name = getTranslation($categories->category_name, $lang);
         return $categories;
      });

      return response()->json($categoriesFinal);
   }

   public function storeCategories(Request $request)
   {
      // dd($request->all());
      $lang = \Lang::getLocale();

      $request->validate(
         [
            "category_ids"  => "required",
            "id"      => "required|exists:brands,id",
         ],
         [
            'category_ids.required'  => 'Category Name is required.',
            'id.required'           => 'Brands is required.',
            'id.exists'             => 'Brands doesn\'t exists.',
         ]
      );
      $categoryNames = array();
      foreach (json_decode($request->category_ids) as $category_id) {
         $categoryBrand = CategoryBrand::updateOrCreate(
            [
               "brand_id"     => $request->id,
               "category_id"  => $category_id,
            ],
            [
               "brand_id"     => $request->id,
               "category_id"  => $category_id,
            ]
         );

         array_push($categoryNames, getTranslation($categoryBrand->category->category_name, $lang));
      }

      return $categoryNames;
   }
   public function saveNewBrand( Request $request ) {

      $check = Brand::all();

      foreach( $check as $brand ) {

         $get_title = ucwords( $brand -> name );

         if( strtolower( $get_title ) == strtolower( $request -> name ) && $brand -> id != $request->id ) {

            return response()->json( [ 'duplicate' => true, 'name' => $request -> name ] );

         }

      }

      $brand = $this->brandRepository->create($request->only('name', 'file', 'website', 'isDefault', 'org_id'));
      return response()->json($brand);
  }
}
