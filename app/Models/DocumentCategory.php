<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    //
    protected $guarded = [];

    protected $appends = ['document_category_name'];

    public function document_types()
    {
        return $this->hasMany(DocumentType::class);
    }

    public function getIsEnglishAttribute()
    {
        return string_to_value('en', $this->name);
    }

    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->name);
    }

    public function getIsItalianAttribute()
    {
        return string_to_value('it', $this->name);
    }

    public function getIsFilipinoAttribute()
    {
        return string_to_value('ph-fil', $this->name);
    }
    
    public function getIsVisayanAttribute()
    {
        return string_to_value('ph-bis', $this->name);
    }

    public function getDocumentCategoryNameAttribute()
    {
        $name = string_to_value(locale(), $this->name);

        $base_name = $this->getIsEnglishAttribute();

        if (is_null($name)) {

            $mesage =  ' <small style="color:red">(No English translation yet)</small>';

            switch (locale()) {
                case 'it':
                    $mesage = $base_name . ' <small style="color:red">(No Italian translation yet)</small>';
                    break;
                case 'de':
                    $mesage = $base_name . ' <small style="color:red">(No German translation yet)</small>';
                    break;
                case 'ph-fil':
                    $mesage = $base_name . ' <small style="color:red">(No Tagalog translation yet)</small>';
                    break;
                case 'ph-bis':
                    $mesage = $base_name . ' <small style="color:red">(No Visayan translation yet)</small>';
                    break;
            }

            $name = $mesage;
        }

        return $name;
    }
}
