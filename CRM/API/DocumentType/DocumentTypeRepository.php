<?php

namespace CRM\API\DocumentType;

use App\Helpers\General\ImageHelper;
use App\Models\DocumentType;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DocumentTypeRepository extends BaseRepository
{
   public function __construct(DocumentType $model)
   {
      $this->model = $model;
   }

   public function getAll($request)
   {
      $documentTypes = $this->model->select('document_types.*')->get();

      return  $documentTypes;
   }

   public function create(array $data)
   {
      return DB::transaction(function () use ($data) {

         $documentType = $this->model;
         $documentType->name = to_json_add(locale(), $data['name']);
         $documentType->document_category_id = $data['document_category_id'];
         $documentType->save();

         return $documentType;
      });
   }

   public function update($id, array $data): DocumentType
   {
      return DB::transaction(function () use ($id, $data) {

         $lang = $data['locale'];

         $documentType = $this->getById($id);
         $documentType->name = to_json_add($lang, $data['name'], to_json_remove($lang, $documentType->name));
         $documentType->document_category_id = $data['document_category_id'];
         $documentType->save();

         return $documentType;
      });
   }
}
