<?php

namespace CRM\API\EmailTemplate;

use App\Helpers\General\ImageHelper;
use App\Models\EmailTemplate;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class EmailTemplateRepository extends BaseRepository
{
   public function __construct(EmailTemplate $model)
   {
      $this->model = $model;
   }

   public function getAll($request)
   {
      $emailTemplates = $this->model->select('email_templates.*')
         ->get();

      return  $emailTemplates;
   }

   public function create(array $data)
   {
      return DB::transaction(function () use ($data) {
         $request = request();


         $emailTemplate = $this->model->create([
            'template_name' => $data['template_name'],
            'preview' => "",
            'variables' => $data['variables'],
            'html_content' => $data['html_content'],
            'others' => "",
         ]);
         
         if ($emailTemplate && $request->hasFile('preview')) {
            $file = $request->file('preview');
            $this->uploadImage($emailTemplate, $file);
         }

         return $emailTemplate;
      });
   }

   public function update($id, array $data): EmailTemplate
   {
      return DB::transaction(function () use ($id, $data) {

         $request = request();

         $emailTemplate = $this->getById($id);

         if ($emailTemplate && $request->hasFile('preview')) {
            $file = $request->file('preview');
            $this->uploadImage($emailTemplate, $file);
         }
         
         $emailTemplate->template_name = $data['template_name'];
         $emailTemplate->variables = $data['variables'];
         $emailTemplate->html_content = $data['html_content'];

         $emailTemplate->save();

         return $emailTemplate;
      });
   }


   public function uploadImage($emailTemplate, $file)
   {
      $image = new ImageHelper([
         'driver' => 's3',
         'id' => $emailTemplate->id,
         's3_storage_path' => 'customer-care-tool',
         's3_folder_path' => config('access.s3_path.email_templates'),
         'file' => $file,
      ]);

      $image->deleteDirectory();

      if ($image->upload()) {
         $emailTemplate->preview =  $image->imageUrl();
         $emailTemplate->save();
      }
   }
}
