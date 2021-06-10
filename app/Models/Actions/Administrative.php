<?php

namespace App\Models\Actions;

use Illuminate\Database\Eloquent\Model;

class Administrative extends Model
{
   protected $table = 'administrative_actions';

   protected $fillable = ['action'];

   protected $appends = ['is_loading', 'action_name', 'is_english', 'is_italian', 'is_german', 'is_filipino', 'is_visayan'];

   public function getIsEnglishAttribute()
   {
      return string_to_value('en', $this->action);
   }

   public function getIsGermanAttribute()
   {
      return string_to_value('de', $this->action);
   }

   public function getIsItalianAttribute()
   {
      return string_to_value('it', $this->action);
   }


   public function getIsFilipinoAttribute()
   {
       return string_to_value('ph-fil', $this->action);
   }

   public function getIsVisayanAttribute()
   {
       return string_to_value('ph-bis', $this->action);
   }

   public function getActionNameAttribute()
   {
      $name = string_to_value(locale(), $this->action);

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
            case 'ph-fil':
               $mesage = $base_name. ' (No Filipino translation yet)';
               break;
            case 'ph-bis':
               $mesage = $base_name. ' (No Visayan translation yet)';
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
}
