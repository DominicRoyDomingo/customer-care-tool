<?php

namespace App\Models\Mails;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mails\CampaignEmail;

class Campaign extends Model
{
   protected $fillable = ['campaign', 'created_by', 'sent_at'];

   protected $appends = ['is_loading', 'campaign_name', 'is_english', 'is_italian', 'is_german'];

   public function getIsEnglishAttribute()
   {
      return string_to_value('en', $this->campaign);
   }

   public function getIsGermanAttribute()
   {
      return string_to_value('de', $this->campaign);
   }

   public function getIsItalianAttribute()
   {
      return string_to_value('it', $this->campaign);
   }

   public function getCampaignNameAttribute()
   {
      $name = string_to_value(locale(), $this->campaign);
      
      $base_name = $this -> getIsEnglishAttribute();
      
      if (is_null($name)) {
         $mesage =  ' (No English translation yet)';
         
         switch (locale()) {
            case 'it':
               $mesage = $base_name. ' (No Italian translation yet)';
               break;
            case 'de':
               $mesage = $base_name. ' (No German translation yet)';
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

   public function creator()
   {
      return $this->belongsTo(User::class, 'created_by');
   }

   public function campaignEmails()
   {
      return $this->hasMany(CampaignEmail::class, 'campaign_id');
   }
}
