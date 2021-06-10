<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticsField extends Model
{
    //
    protected $guarded = [];
    
    protected $appends = ['is_loading', 'display_name', 'is_english', 'is_italian', 'is_german'];
        

    public function table(){
        return $this->belongsTo(StatisticsTable::class, 'statistics_table_id');
    }
    
    public function extension(){
        return $this->hasOne(StatisticsExtension::class, 'statistics_field_id');
    }

    public function getIsEnglishAttribute()
    {
        return string_to_value('en', $this->display);
    }

    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->display);
    }

    public function getIsItalianAttribute()
    {
        return string_to_value('it', $this->display);
    }
    

    public function getDisplayNameAttribute()
    {
        $name = string_to_value(locale(), $this->display);

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

    public function getIsLoadingAttribute()
    {
        return false;
    }
}
