<?php

namespace CRM\API\ContactType;

use App\Models\ContactType;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class ContactTypeRepository extends BaseRepository
{
   public function __construct(ContactType $model)
   {
      $this->model = $model;
   }

   public function getAll()
   {
      $contact_types = $this->model->select('contact_types.*')
         ->get();

      return  $contact_types;
   }

   public function create(array $data)
   {
      return DB::transaction(function () use ($data) {
         $contact_type = $this->model->create([
            'type_name' => to_json_add(locale(), $data['type_name'])
         ]);

         return $contact_type;
      });
   }

   public function update($id, array $data): ContactType
   {
      return DB::transaction(function () use ($id, $data) {

         $lang = $data['locale'];

         $contact_type = $this->getById($id);

         $contact_type->type_name = to_json_add($lang, $data['type_name'], to_json_remove($lang, $contact_type->type_name));
         
         $contact_type->save();

         return $contact_type;
      });
   }
}
