<?php

namespace CRM\API\EmailTemplate;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EmailTemplateController extends Controller
{
   private $emailTemplateRepository;

   public function __construct(EmailTemplateRepository $emailTemplateRepository)
   {
      $this->emailTemplateRepository = $emailTemplateRepository;
   }

   public function index(Request $request)
   {
      $emailTemplates = $this->emailTemplateRepository->getAll($request);

      return response()->json($emailTemplates);
   }

   public function show(Request $request, $id)
   {
      $emailTemplate = $this->emailTemplateRepository->getById($id);
      
      return response()->json($emailTemplate);
   }

   // for Category here
   public function store(Request $request)
   {
      $emailTemplate = null;

      if (is_null($request->id)) {
         $request->validate([
            'template_name' => 'required|min:2|unique:email_templates'
         ]);

         $emailTemplate = $this->emailTemplateRepository->create($request->only('template_name', 'html_content', 'preview', 'variables'));
      } else {
         $emailTemplate = $this->emailTemplateRepository->update($request->id, $request->only('template_name', 'html_content', 'preview', 'variables'));
      }
      
      $emailTemplate->responseStatus = true;

      return response()->json($emailTemplate);
      // return response()->json($request->all());

      // if (is_null($request->id)) {
      //    $sameEmailTemplate = EmailTemplate::where('name', $request->name)->first();

      //    if ($sameEmailTemplate != null) {
      //       throw ValidationException::withMessages(['request' => $request->name . ' already exists']);
      //    }

      //    $this->emailTemplateRepository->create($request->only('name', 'file', 'website'));
      // } else {

      //    $this->emailTemplateRepository->update($request->id, $request->only('category', 'name', 'logo', 'website'));
      // }

      // return response()->json(true);
   }

   public function destroy(Request $request)
   {
      $emailTemplate = $this->emailTemplateRepository->getById($request->id);

      if ($emailTemplate->emailTemplate_clients->count() > 0) {
         throw ValidationException::withMessages(['emailTemplate' =>  strtoupper(
            $emailTemplate->name
         ) . ' is used and cannot be deleted.']);
      }

      if ($emailTemplate->emailTemplate_engagements > 0) {
         throw ValidationException::withMessages(['emailTemplate' =>  strtoupper(
            $emailTemplate->name
         ) . ' is used and cannot be deleted.']);
      }
      
      if ($emailTemplate->emailTemplate_platforms->count() > 0) {
         throw ValidationException::withMessages(['emailTemplate' =>  strtoupper(
            $emailTemplate->name
         ) . ' is used and cannot be deleted.']);
      }
      
      $emailTemplate->delete();

      Storage::disk('s3')->deleteDirectory(config('access.s3_path.email_templates') . '/' . $emailTemplate->id);

      return response()->json(true);
   }
}
