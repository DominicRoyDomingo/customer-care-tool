<?php

namespace App\Models\Mails;

use Illuminate\Database\Eloquent\Model;

use App\Models\Brand;
use App\Models\EmailTemplate;
use App\Models\Mails\Campaign;
use App\Models\Mails\Recipient;

class CampaignEmail extends Model
{
   protected $fillable = [
      'sender_name',
      'sender_email',
      'subject',
      'content',
      'type',
      'campaign_id',
      'status',
      'brand_id',
      'variables',
      'email_template_id',
   ];

   protected $appends = ['is_loading', 'content_name', 'subject_name', 'is_english', 'is_italian', 'is_german'];

   public function getIsEnglishAttribute()
   {
      return [
         'content' => string_to_value('en', $this->content),
         'subject' => string_to_value('en', $this->subject)
      ];
   }

   public function getIsGermanAttribute()
   {
      return [
         'content' => string_to_value('de', $this->content),
         'subject' => string_to_value('de', $this->subject)
      ];
   }

   public function getIsItalianAttribute()
   {
      return [
         'content' => string_to_value('it', $this->content),
         'subject' => string_to_value('it', $this->subject)
      ];
   }

   public function getContentNameAttribute()
   {
      $name = string_to_value(locale(), $this->content);

      if (is_null($name)) {
         $mesage = '<i class="text-small text-danger">No English Translation yet.</i>';

         switch (locale()) {
            case 'it':
               $mesage = '<i class="text-small text-danger">No Italian Translation yet.</i>';
               break;
            case 'de':
               $mesage = '<i class="text-small text-danger">No German Translation yet.</i>';
               break;
         }

         $name = $mesage;
      }

      return $name;
   }

   public function getSubjectNameAttribute()
   {
      $name = string_to_value(locale(), $this->subject);

      if (is_null($name)) {
         $mesage = '<i class="text-small text-danger">No English Translation yet.</i>';

         switch (locale()) {
            case 'it':
               $mesage = '<i class="text-small text-danger">No Italian Translation yet.</i>';
               break;
            case 'de':
               $mesage = '<i class="text-small text-danger">No German Translation yet.</i>';
               break;
         }

         $name = $mesage;
      }

      return $name;
   }

   public function getIsLoadingAttribute()
   {
      return false;
   }

   public function brand()
   {
      return $this->belongsTo(Brand::class, 'brand_id');
   }

   public function campaign()
   {
      return $this->belongsTo(Campaign::class, 'campaign_id');
   }
   
   public function getVariablesAttribute($value)
   {
      return json_decode($value, true);
   }

   public function email_template()
   {
      return $this->belongsTo(EmailTemplate::class, 'email_template_id');
   }

   public function recipients()
   {
      return $this->hasMany(Recipient::class, 'campaign_email_id');
   }
}
