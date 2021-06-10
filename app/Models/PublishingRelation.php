<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublishingRelation extends Model
{
    protected $table = 'publishing_relation';

    protected $hidden = ['linked_publishing_relation'];

    protected $guarded = [];

    protected $appends = ['publishing_relation_name', 'linked_publishing_relation_name', 'organization'];

    public function publishing_relation()
    {
        return $this->belongsTo(Publishing::class, 'publishing', 'id');
    }

    public function linked_publishing_relation()
    {
        return $this->belongsTo(Publishing::class, 'linked_publishing', 'id');
    }

    public function getPublishingRelationNameAttribute()
    {
        $publishingRelationName = getTranslation(Publishing::find($this->attributes['publishing'])->name, locale());
        return $publishingRelationName;
    }

    public function getLinkedPublishingRelationNameAttribute()
    {
        $linkedPublishingRelationName = getTranslation(Publishing::find($this->attributes['linked_publishing'])->name, locale());
        return $linkedPublishingRelationName;
    }

    public function getOrganizationAttribute()
    {
        $organization = $this->linked_publishing_relation->item->organization;
        return $organization;
    }

    
}
