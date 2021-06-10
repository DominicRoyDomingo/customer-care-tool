<?php

namespace CRM\API\Engagement;

use App\Models\Actions\Engagement;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class EngagementRepository extends BaseRepository
{
   public function __construct(Engagement $model)
   {
      $this->model = $model;
   }

   public function getAll($request)
   {
      $engagements = $this->model->select('engagements.*')
         ->get();

      return $engagements;
   }

   // for engagement
   public function create(array $data)
   {
      return DB::transaction(function () use ($data) {
         $engagement = $this->model;
         $engagement->engagement = to_json_add(locale(), $data['engagement']);
         $engagement->brands = json_encode($data['brands']);
         $engagement->save();

         return $engagement;
      });
   }

   public function update($id, array $data): Engagement
   {
      return DB::transaction(function () use ($id, $data) {

         $lang = $data['locale'];

         $engagement = $this->getById($id);
         $engagement->engagement = to_json_add($lang, $data['engagement'], to_json_remove($lang, $engagement->engagement));
         $engagement->brands = json_encode($data['brands']);
         $engagement->save();

         return $engagement;
      });
   }
}
