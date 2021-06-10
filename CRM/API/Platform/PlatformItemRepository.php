<?php

namespace CRM\API\Platform;

use App\Models\PlatformItem;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PlatformItemRepository extends BaseRepository
{
   public function __construct(PlatformItem $model)
   {
      $this->model = $model;
   }

   public function getAll($request)
   {
      $platform_items = $this->model->select('platform.*')
         ->with(['platformType'])
         ->get();

      return $platform_items;
   }

   // for engagement
   public function create(array $data)
   {
      return DB::transaction(function () use ($data) {
         $platformItem = $this->model->create([
            'brand' => $data["brand_id"],
            'platform_type' => $data["platform_type_id"]
         ]);

         return $platformItem;
      });
   }

   public function update($id, array $data): Engagement
   {
      return DB::transaction(function () use ($id, $data) {
         $platformItem = $this->getById($id);

         $platformItem->brand = $data["brand_id"];

         $platformItem->platform_type = $data["platform_type_id"];

         $platformItem->save();

         return $platformItem;
      });
   }
}
