<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tags;

class PublishingTag extends Model
{
    protected $table = 'publishing_tags';

    protected $guarded = [];

    protected $appends = ['tags_name'];

    public function publishing() {

        return $this->belongsTo(Publishing::class);

    }
    
    public function getTagsNameAttribute()
    {
        $tagsNames = getTranslation(Tags::find($this->attributes['tag'], ['id', 'name'])->name, locale());
        return $tagsNames;
        $tags = collect($tagsNames)->map(function ($tag) {

            // $tag['id']   = $tag['id'];
            $tag['name'] = getTranslation($tag['name'], locale());
        
            return $tag;
        
        });
        // $tagsNamesTranslated = $tagsNames->map(function($tag, $key){
        //     return getTranslation($tag->name, locale()); 
        // });

        return $tags;
    }
}
