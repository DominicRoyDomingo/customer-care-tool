<?php

namespace CRM\API\Platform;

use App\Models\PlatformType;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PlatformTypeRepository extends BaseRepository
{
   public function __construct(PlatformType $model)
   {
      $this->model = $model;
   }

   public function getAll($request)
   {
      $platform_type = $this->model->select('platform_type.*')
         ->get();

      return $platform_type;
   }

   // for engagement
   public function create(array $data)
   {
      return DB::transaction(function () use ($data) {
         $platformType = $this->model->create([
            'name' =>  to_json_add(locale(), $data['name'])
         ]);

         return $platformType;
      });
   }

   public function update($id, array $data): Engagement
   {
      return DB::transaction(function () use ($id, $data) {
         $platformType = $this->getById($id);

         $platformType->brand = $data["brand_id"];

         $platformType->platform_type = $data["platform_type_id"];

         $platformType->save();

         return $platformType;
      });
   }
}
