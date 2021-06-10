<?php

namespace App\Models\Actions;

use Illuminate\Database\Eloquent\Model;

/**
 * Activity Model
 */
class Activity extends Model
{
    /**
     * Define variables
     *
     * @var array $fillable
     * @var array $appends
     */
    protected $fillable = ['activity'];
    protected $appends = ['is_loading', 'activity_name', 'is_english', 'is_italian', 'is_german', 'is_filipino', 'is_visayan' ];

    /**
     * getIsEnglishAttribute
     *
     * @return void
     */
    public function getIsEnglishAttribute()
    {
        return string_to_value('en', $this->activity);
    }

    /**
     * getIsGermanAttribute
     *
     * @return void
     */
    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->activity);
    }

    /**
     * getIsItalianAttribute
     *
     * @return void
     */
    public function getIsItalianAttribute()
    {
        return string_to_value('it', $this->activity);
    }

    public function getIsFilipinoAttribute()
    {
        return string_to_value('ph-fil', $this->activity);
    }

    public function getIsVisayanAttribute()
    {
        return string_to_value('ph-bis', $this->activity);
    }

    /**
     * getActivityNameAttribute
     *
     * @return void
     */
    public function getActivityNameAttribute()
    {
        $name = string_to_value(locale(), $this->activity);

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

    /**
     * getIsLoadingAttribute
     *
     * @return void
     */
    public function getIsLoadingAttribute()
    {
        return false;
    }
}
