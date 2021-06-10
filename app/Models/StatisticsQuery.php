<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticsQuery extends Model
{
    //
    protected $guarded = [];
    
    protected $appends = ['localized_text'];

    public function getIsEnglishAttribute()
    {
        return string_to_value('en', $this->question_text);
    }

    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->question_text);
    }

    public function getIsItalianAttribute()
    {
        return string_to_value('it', $this->question_text);
    }
    

    public function getLocalizedTextAttribute()
    {
        $name = string_to_value(locale(), $this->question_text);

        $base_name = $this -> getIsEnglishAttribute();
      
        if (is_null($name)) {

            $mesage =  ' <small style="color:red">(No English translation yet)</small>';

            switch (locale()) {
                case 'it':
                $mesage = $base_name. ' <small style="color:red">(No Italian translation yet)</small>';
                break;
                case 'de':
                $mesage = $base_name. ' <small style="color:red">(No German translation yet)</small>';
                break;
            }

            $name = $mesage;
        }
        
        return $name;
    }

}
