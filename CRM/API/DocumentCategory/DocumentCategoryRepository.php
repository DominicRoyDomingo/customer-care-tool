<?php

namespace CRM\API\DocumentCategory;

use App\Helpers\General\ImageHelper;
use App\Models\DocumentCategory;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DocumentCategoryRepository extends BaseRepository
{
   public function __construct(DocumentCategory $model)
   {
      $this->model = $model;
   }

   public function getAll($request)
   {
      $documentCategories = $this->model->select('document_categories.*')->get();
      return  $documentCategories;
   }

   public function create(array $data)
   {
      return DB::transaction(function () use ($data) {

         $documentCategory = $this->model;
         $documentCategory->name = to_json_add(locale(), $data['name']);
         $documentCategory->save();

         return $documentCategory;
      });
   }

   public function update($id, array $data): DocumentCategory
   {
      return DB::transaction(function () use ($id, $data) {

         $lang = $data['locale'];

         $documentCategory = $this->getById($id);
         $documentCategory->name = to_json_add($lang, $data['name'], to_json_remove($lang, $documentCategory->name));
         $documentCategory->save();

         return $documentCategory;
      });
   }
}
