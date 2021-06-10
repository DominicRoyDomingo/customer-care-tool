<?php

namespace CRM\API\Campaign;

use App\Helpers\General\ImageHelper;
use App\Models\Mails\Campaign;
use App\Models\Mails\CampaignEmail;
use App\Repositories\BaseRepository;
use App\Models\Mails\Recipient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CampaignRepository extends BaseRepository
{
   public function __construct(Campaign $model)
   {
      $this->model = $model;
   }

   public function getAll($request)
   {
      /*
      $data = $this->model
         ->select('campaigns.*')
         ->with([
            'creator',
            'campaignEmails.brand',
            'campaignEmails.recipients.profile'
         ])
         ->get();
      */
 
      if(\Auth::user()->hasRole('executive')) {
         $organizations = \Auth::user()->organizations;

         $user_ids = [];

         foreach($organizations as $organization){
            $user_ids = array_merge($user_ids, $organization->user_ids);
         }

         
         $data = $this->model
         ->select('campaigns.*')
         ->whereIn('created_by', $user_ids)
         ->with([
            'creator'
         ])
         ->get();
      } else {
         $data = $this->model
         ->select('campaigns.*')
         ->with([
            'creator'
         ])
         ->get();
      }
      
      return $data;
      
   }

   public function show($id)
   {
      $data = $this->model
         ->select('campaigns.*')
         ->with([
            'creator',
            'campaignEmails.brand',
            'campaignEmails.recipients.profile'
         ])
         ->where('id', $id)
         ->first();
 
      return $data;
   }

   public function create_campaign(array $data)
   {
      return DB::transaction(function () use ($data) {

         $campaign = $this->model->create([
            'campaign' => to_json_add($data['locale'], $data['campaign']),
            'created_by' =>   auth()->user()->id
         ]);

         return $campaign;
      });
   }

   public function update_campaign($id, array $data)
   {
      return DB::transaction(function () use ($id, $data) {

         $lang = $data['locale'];

         $campaign = $this->getById($id);

         $campaign->campaign = to_json_add($lang, $data['campaign'], to_json_remove($lang, $campaign->campaign));

         $campaign->save();

         return $campaign;
      });
   }
/*
   public function create(array $data)
   {
      return DB::transaction(function () use ($data) {

         $isEmailed = $data['type'] === 'send' ? true : false;

         $campaignEmail = CampaignEmail::create([
            'sender_name' => $data['sender_name'],
            'sender_email' => $data['sender_email'],
            'content' => !is_null($data['content']) ? to_json_add($data['locale'], $data['content']) : null,
            'subject' =>  !is_null($data['subject']) ? to_json_add($data['locale'], $data['subject']) : null,
            'brand_id' => $data['brand'],
            'type' => 'html',
            'campaign_id' =>  $data['id'],
         ]);

         $recipients = $data['recipient'];

         if (isset($recipients['id']) && $recipients['id'] === 'all') {

            // Get Recipient By Branch
            $recipients = $this->getRecipientByBrand($data['brand']);

            // Store All Recipients
            $this->storedRecipients($recipients, $campaignEmail, $isEmailed);
         } elseif (!isset($recipients['id']) && !empty($recipients)) {

            // Store Selected Recipients
            $this->storedRecipients($recipients, $campaignEmail, $isEmailed);
         }

         if ($isEmailed) {
            $campaignEmail->update(['status' => "sent"]);
            $campaignEmail->campaign->update(['sent_at' => now()]);
         }

         return $campaignEmail;
      });
   }
*/

   public function create(array $data)
   {
      return DB::transaction(function () use ($data) {

         $isEmailed = $data['type'] === 'send' ? true : false;
            
         $campaignEmail = CampaignEmail::create([
            'sender_name' => $data['sender_name'],
            'sender_email' => $data['sender_email'],
            'content' => !is_null($data['content']) ? to_json_add($data['locale'], $data['content']) : null,
            'subject' =>  !is_null($data['subject']) ? to_json_add($data['locale'], $data['subject']) : null,
            'brand_id' => $data['brand'],
            'type' => 'html',
            'campaign_id' =>  $data['id'],
            'email_template_id' => $data['template_id'],
            'variables' => "",
         ]);

         if(isset($data["campaign_name"])){
            $campaignEmail->campaign->update(['campaign' => to_json_add($data['locale'], $data['campaign_name'])]);
         }

         $variables = [];
         $request_array = $data;
         
         $request = request();

         foreach(json_decode($data["variables"], true) as $variable){
            $var_val = "";
            if ($campaignEmail && $request->hasFile($variable)) {
               $file = $request->file($variable);
               $var_val = $this->uploadImage($campaignEmail, $variable, $file);
            }
            else{
               $var_val = $request_array[$variable];
            }

            $variables[] = [
               $variable => $var_val
            ];
         }

         $campaignEmail->variables = json_encode($variables);
         $campaignEmail->save();

         $recipients = json_decode($data['recipient'], true);
         
         if (isset($recipients['id']) && $recipients['id'] === 'all') {
            // Get Recipient By Branch
            $recipients = $this->getRecipientByBrand($data['brand']);

            // Store All Recipients
            $this->storedRecipients($recipients, $campaignEmail, $isEmailed);
         } elseif (!isset($recipients['id']) && !empty($recipients)) {
            // Store Selected Recipients
            $this->storedRecipients($recipients, $campaignEmail, $isEmailed);
         }

         if ($isEmailed) {
            $campaignEmail->update(['status' => "sent"]);
            $campaignEmail->campaign->update(['sent_at' => now()]);
         }

         return $campaignEmail;
      });
   }

   public function sendEmailToCustomer($request)
   {
      
      $data = [
         'sender_name' => $request->sender_name,
         'sender_email' => $request->sender_email,
         'content' =>  $request->content ? to_json_add($request->locale, $request->content) : null,
         'subject' => $request->subject ? to_json_add($request->locale, $request->subject) : null,
         'type' => 'html',
         'status' => "sent",
         'brand_id' =>  $request->brand,
         'campaign_id' =>  $request->id,
         'email_template_id' => $request->template_id,
         'variables' => "",
      ];

      $campaignEmail = CampaignEmail::create($data);

      $variables = [];
      $request_array = json_decode(json_encode($request->all()),true);

      foreach(json_decode($request->variables, true) as $variable){
         $var_val = "";
         if ($campaignEmail && $request->hasFile($variable)) {
            $file = $request->file($variable);
            $var_val = $this->uploadImage($campaignEmail, $variable, $file);
         }
         else{
            $var_val = $request_array[$variable];
         }

         $variables[] = [
            $variable => $var_val
         ];
      }

      $campaignEmail->variables = json_encode($variables);
      $campaignEmail->save();
      


      $recipients = json_decode($request->recipient, true);

      if (isset($recipients['id']) && $recipients['id'] === 'all') {
         $recipients = $this->getRecipientByBrand($request->brand);
      }

      $this->storedRecipients($recipients, $campaignEmail, true);

      $campaignEmail->campaign->update([
         'sent_at' => now(),
      ]);

      return "campaign sent";
   }

   public function sendCampaignEmail($request)
   {
      $campaignEmail = CampaignEmail::findOrfail($request["id"]);

      $recipients = $campaignEmail->recipients;

      //create receipients
      foreach ($recipients as $recipient) {
         $this->email($recipient->profile, $campaignEmail);
      }

      $campaignEmail->campaign->update([
         'sent_at' => now(),
      ]);

      return "campaign sent";
   }
   
   public function email($profile, $campaignEmail)
   {
      $html_content = $this->translate_variables($campaignEmail->email_template->html_content, $profile, $campaignEmail);
      
      $data = [
         'campaignEmail' => $campaignEmail,
         'profile' => $profile,
         'html_content' => $html_content,
      ];

      Mail::send('backend.emails.campaign', $data, function ($message) use ($profile, $html_content, $campaignEmail) {
         $message
            ->from($campaignEmail->sender_email, $campaignEmail->sender_name)
            ->replyTo($campaignEmail->sender_email, $campaignEmail->sender_name)
            ->to($profile->primary_email)
            ->subject($campaignEmail->subject_name);
      });
   }

   public function storedRecipients($items, $eCampaign, $isEmailed = false)
   {
      //Delete all receipients under campaign email id
      Recipient::where(['campaign_email_id' => $eCampaign->id])->delete();
     
      //create receipients
      foreach ($items as $item) {
         $recipient = Recipient::create([
            'recipient' => !is_array($items) && is_object($items) ? $item->id : $item['id'],
            'campaign_email_id' =>  $eCampaign->id,
         ]);

         //Email to receipient
         //if ($isEmailed) $this->emailed($recipient->profile, $eCampaign->campaign);

         if ($isEmailed) $this->emailed($recipient->profile, $eCampaign);
      }
   }

   public function emailed($profile, $campaignEmail)
   {
      $request = request();

      $html_content = $this->translate_variables($campaignEmail->email_template->html_content, $profile, $campaignEmail);
      
      $data = [
         'campaignEmail' => $campaignEmail,
         'profile' => $profile,
         'html_content' => $html_content,
      ];

      Mail::send('backend.emails.campaign', $data, function ($message) use ($profile, $html_content, $request) {
         $user = $request->user();
         $message
            ->from($request->sender_email, $request->sender_name)
            ->replyTo($request->sender_email, $request->sender_name)
            ->to($profile->primary_email)
            ->subject($request->subject);
      });
   }

   public function show_email($id){
      $campaignEmail = CampaignEmail::findOrfail($id);
      
      //Delete all receipients under campaign email id
      $recipient = Recipient::where(['campaign_email_id' => $campaignEmail->id])->first();
      
      if($recipient == null){
         $recipient = new \stdClass();
         $recipient->profile = new \stdClass();
         $recipient->profile->firstname = "Test";
         $recipient->profile->surname = "Recipient";
      }
      
      $campaignEmail->preview = $this->translate_variables($campaignEmail->email_template->html_content, $recipient->profile, $campaignEmail);
      
      return $campaignEmail;  
   }

   public function translate_variables($html_content, $profile, $campaignEmail){
      //Translate Default Variables
      $html_content = str_replace("%brand_logo%", $campaignEmail->brand->logo, $html_content);
      $html_content = str_replace("%brand_name%", $campaignEmail->brand->name, $html_content);
      $html_content = str_replace("%campaign_name%", $campaignEmail->campaign->campaign_name, $html_content);
      $html_content = str_replace("%content_name%", $campaignEmail->content_name, $html_content);
      $html_content = str_replace("%profile_name%", ucwords($profile->firstname) . " " . ucwords($profile->surname), $html_content);
      $html_content = str_replace("%first_name%", ucwords($profile->firstname), $html_content);
      $html_content = str_replace("%surname%",  ucwords($profile->surname), $html_content);

      //Translate custom variables
      $variables = json_decode(json_encode($campaignEmail->variables), true);
      $template_variables = $campaignEmail->email_template->variables;
      
      foreach($variables as $variable){
         $variable_name = key($variable);
         $variable_value = $variable[$variable_name];
         foreach($template_variables as $template_variable){
            if($variable_name == $this->createSlug($template_variable->variable_name)){
               $html_content = str_replace("%" . $template_variable->variable_text . "%",  $variable_value, $html_content);
            }   
         }
      }


      return $html_content;
   }

   public static function createSlug($str, $delimiter = '-'){

      $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
      return $slug;
  
  } 

   public function getRecipientByBrand($id)
   {
      $profiles = DB::table('profiles')
         ->join('brand_clients', 'brand_clients.profile_id', '=', 'profiles.id')
         ->whereNotNull('profiles.primary_email')
         ->where('brand_clients.brand_id', $id)
         ->select('profiles.*')
         ->get();

      return $profiles;
   }
   
   public function uploadImage($emailTemplate, $variable_slug, $file)
   {
      $image = new ImageHelper([
         'driver' => 's3',
         'id' =>  "/" . $emailTemplate->id . "/"  . $variable_slug,
         's3_storage_path' => 'customer-care-tool',
         's3_folder_path' => config('access.s3_path.campaign_emails'),
         'file' => $file,
      ]);

      $image->deleteDirectory();

      if ($image->upload()) {
         return $image->imageUrl();
      }
      else{
         return false;
      }
   }
}
