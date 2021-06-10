<?php

namespace CRM\API\Administrative;

use App\Models\Actions\Administrative;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class AdministrativeRepository extends BaseRepository
{
   public function __construct(Administrative $model)
   {
      $this->model = $model;
   }

   public function getAll($request)
   {
      return $this->model->get();

      $data = $this->model
         ->select('administrative_actions.*')
         ->get();

      return $data;
   }

   public function create(array $data)
   {
      return DB::transaction(function () use ($data) {
         $action = $this->model->create([
            'action' => to_json_add(locale(), $data['action'])
         ]);

         return $action;
      });
   }

   public function update($id, array $data): Administrative
   {
      return DB::transaction(function () use ($id, $data) {

         $lang = $data['locale'];

         $action = $this->getById($id);

         $action->action = to_json_add($lang, $data['action'], to_json_remove($lang, $action->action));

         $action->save();

         return $action;
      });
   }
}
