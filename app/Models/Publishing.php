<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publishing extends Model
{
    protected $table = 'publishing';

    protected $guarded = [];

    protected $appends = ['publishing_tags'];

    public function item() {

        return $this->belongsTo(ContentItem::class, 'item_id');

    }

    public function publishing_histories() {

        return $this->hasOne(PublishingHistory::class)->latest();
    }

    public function getPublishingTagsAttribute() {

        $tags = PublishingTag::where('publishing_id', $this->id)->get();
        $tagsArr = array();
        foreach ($tags as $tag ) {
            array_push( $tagsArr, $tag->tag );
        }
        return $tagsArr;
    }

    public function publishing_tag() {

        return $this->hasMany(PublishingTag::class, 'publishing_id', 'id');
    }

    public function publishing_relation()
    {
        return $this->hasMany(PublishingRelation::class, 'publishing', 'id');
    }

    public function getItemNameAttribute() {

        return $this->item->item_name;

    }

    public function getPublishingHistoryAttribute()
    {

        return PublishingHistory::where('publishing_id', $this->id)->orderBy('updated_at', 'DESC')->first();
        
    }
}
