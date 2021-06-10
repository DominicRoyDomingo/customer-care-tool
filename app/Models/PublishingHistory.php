<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Auth\User;

class PublishingHistory extends Model
{
    protected $table = 'publishing_history';

    protected $guarded = [];

    public function publishing() {

        return $this->belongsTo(Publishing::class, 'publishing_id');
        
    }

    public function dateStatus() {

        return $this->belongsTo(DateStatus::class, 'status');

    }

    public function platformItem() {

        return $this->belongsTo(PlatformItem::class, 'platform');

    }

    public function publisher_object() {
        return $this->belongsTo(User::class, 'publisher');
    }

    public function author_object() {
        return $this->belongsTo(User::class, 'author');
    }

    public function getStatusNameAttribute() {

        return $this->dateStatus->status;
        
    }

    public function getPlatformNameAttribute() {

        return $this->platformItem->platform_name;
        
    }

    public function getBrandLogoAttribute() {

        return $this->platformItem->brand_logo;
        
    }

    public function getPublisherNameAttribute()
    {
        $publisherName = User::select('id', 'first_name', 'last_name')->where('id', $this->attributes['publisher'])->first();

        return $publisherName->full_name;
    }

    public function getAuthorNameAttribute()
    {
        $authorName = User::select('id', 'first_name', 'last_name')->where('id', $this->attributes['author'])->first();

        return $authorName->full_name;
    }

    public function getDateSequenceAttribute()
    {
        $dateStatus = DateStatus::select('sequence')->where('id', $this->attributes['status'])->first();

        return $dateStatus->sequence;
    }

    public function getDateStatusNameAttribute()
    {
        $dateStatus = DateStatus::select('status')->where('id', $this->attributes['status'])->first();

        return $dateStatus->status;
    }
}
