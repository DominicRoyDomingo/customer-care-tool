<?php

namespace CRM\API\Brand;

use App\Helpers\General\ImageHelper;
use App\Models\Brand;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class BrandRepository extends BaseRepository
{
   public function __construct(Brand $model)
   {
      $this->model = $model;
   }

   public function getAll($request)
   {
      $brands = $this->model->select('brands.*')
         ->when($request->org_id, function ($query) use ($request) {
            $query->where('organization', $request->org_id);
         })
         ->when($request->filter, function ($query) use ($request) {
            $query->where('name', 'LIKE', "%{$request->filter}%");
         })
         ->with([
            'categories' => function ($query) {
               $query->select('id', 'brand_id', 'category_id');
            },
            'brand_categories' => function ($query) {
               $query->select('id', 'brand', 'category');
            },
         ])
         ->get();

      return  $brands;
   }

   public function create(array $data)
   {
      
      return DB::transaction(function () use ($data) {
         $request = request();
         if ($data['isDefault'] != null) {
            $brand = $this->model->select('brands.*')->where('isDefault', 1)->first();
            if ($brand != null) {
               $brand->isDefault = 0;
               $brand->save();
            }
         }
         
         $brand = $this->model->create([
            'name' => $data['name'],
            'website' => $data['website'],
            'isDefault' => $data['isDefault'] == null ? 0 : $data['isDefault'],
            'organization' => $data['org_id'],
         ]);

         if( isset( $data['categories'] ) && ! empty( $data['categories'] ) ) {
            foreach($data['categories'] as $category) {
               $brand->brand_categories()->create([
                  'category' => $category,
               ]);
            }
         }

         if ($brand && $request->hasFile('file')) {

            $file = $request->file('file');

            $this->uploadImage($brand, $file);
         }

         return $brand;
      });
   }

   public function update($id, array $data): Brand
   {
      return DB::transaction(function () use ($id, $data) {

         $request = request();
         $brand = $this->getById($id);
         // dd($brand);
         if ($data['isDefault'] != null) {
            $brandHasDefault = $this->model->select('brands.*')->where('isDefault', 1)->first();
            if ($brandHasDefault != null) {
               $brandHasDefault->isDefault = 0;
               $brandHasDefault->save();
            }
         }
         $brand->brand_categories()->delete();

         foreach($data['categories'] as $category) {
            $brand->brand_categories()->create([
               'category' => $category,
            ]);
         }
         
         if ($brand && $request->hasFile('file')) {

            $file = $request->file('file');

            $this->uploadImage($brand, $file);
         }

         $brand->name = $data['name'];
         $brand->website = $data['website'];
         $brand->isDefault = $data['isDefault'] == null ? 0 : $data['isDefault'];

         $brand->save();

         $brand = $this->model->with([
            'categories' => function ($query) {
               $query->select('id', 'brand_id', 'category_id');
            },
            'brand_categories' => function ($query) {
               $query->select('id', 'brand', 'category');
            },
         ])->find($id);

         return $brand;
      });
   }


   public function uploadImage($brand, $file)
   {
      $image = new ImageHelper([
         'driver' => 's3',
         'id' => $brand->id,
         's3_storage_path' => 'customer-care-tool',
         's3_folder_path' => config('access.s3_path.brands'),
         'file' => $file,
      ]);

      $image->deleteDirectory();

      if ($image->upload()) {
         $brand->logo =  $image->imageUrl();
         $brand->save();
      }
   }
}
