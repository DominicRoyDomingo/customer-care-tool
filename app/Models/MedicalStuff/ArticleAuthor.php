<?php

namespace App\Models\MedicalStuff;

use App\Models\Actor;
use Illuminate\Database\Eloquent\Model;
use App\Models\MedicalStuff\MedicalArticle;

class ArticleAuthor extends Model
{
    protected $table = 'article_authors';
    protected $guarded = [];
    protected $appends = [
        'actors'
     ];

    public function medical_articles()
    {
        return $this->hasMany(MedicalArticle::class, 'id');
    }

    public function type(){
        return $this->belongsTo(AuthorType::class, 'author_type');
    }

    public function getActorsAttribute()
    {
       $authors = json_decode($this->authors) ?: [];
 
       return collect($authors)
          ->map(
             function ($id) {
                return Actor::find($id);
             }
          )
          ->values();
    }

}
