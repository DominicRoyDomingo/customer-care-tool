<?php

namespace App\Models\MedicalStuff;

use Illuminate\Database\Eloquent\Model;
use App\Models\MedicalStuff\MedicalTerm;

class Icon extends Model
{
    protected $table = 'term_icon';
    protected $guarded = [];

    protected $appends = [
        'medical_term_item',
        'medical_term_provider_type_item'
    ];

    public function medical_term()
    {
        return $this->belongsTo(MedicalTerm::class, 'term', 'id');
    }

    public function getMedicalTermItemAttribute()
    {
        return $this->medical_term;
    }

    public function medical_term_provider_type()
    {
        return $this->belongsTo(MedicalTerm::class, 'provider_type', 'id');
    }

    public function getMedicalTermProviderTypeItemAttribute()
    {
        return $this->medical_term_provider_type;
    }

    // public function getHtmlTagItemsAttribute()
    // {   
    //     $htmlTags = HtmlTag::findMany(json_decode($this->html_tags));
    //     return $htmlTags;
    // }
}
