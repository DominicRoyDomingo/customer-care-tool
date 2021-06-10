<?php

namespace App\Models\MedicalStuff;

use Illuminate\Database\Eloquent\Model;
use App\Models\MedicalStuff\MedicalArticle;
use App\Models\MedicalStuff\HtmlTag;

class ArticleImage extends Model
{
    protected $table = 'article_images';
    protected $guarded = [];

    protected $appends = [
        'html_tag_items'
    ];

    public function medical_articles()
    {
        return $this->hasMany(MedicalArticle::class, 'id');
    }

    public function getHtmlTagItemsAttribute()
    {   
        $htmlTags = HtmlTag::findMany(json_decode($this->html_tags));
        return $htmlTags;
    }

}
