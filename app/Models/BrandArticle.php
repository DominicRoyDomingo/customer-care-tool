<?php

namespace App\Models;

use App\Models\MedicalStuff\MedicalArticle;
use Illuminate\Database\Eloquent\Model;

class BrandArticle extends Model
{
    protected $table = 'brand_articles';
    protected $guarded = [];

    public function articles() {
        return $this->belongsTo(MedicalArticle::class, 'article_id');
    }

}
