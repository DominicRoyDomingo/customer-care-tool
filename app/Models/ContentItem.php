<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\DateStatus;

use App\Models\ItemType;

use App\Models\Auth\User;

class ContentItem extends Model
{
    protected $table = 'items';

    protected $guarded = [];


    public function item_history() {

        return $this->hasOne(ItemHistory::class, 'item', 'id')->latest();
        
    }


    public function date_status() {

        return $this->belongsTo(DateStatus::class, 'status');

    }

    public function item_type_object() {

        return $this->belongsTo(ItemType::class, 'item_type');

    }

    public function content_name() {

        return $this->belongsTo(Content::class, 'content');

    }

    public function publishings() {
        return $this->hasMany(Publishing::class, 'item_id');
    }


    // public function getDateStatusAttribute()
    // {
    //     return DateStatus::select('status')->where('id', $this->status)->first();
    // }

    // public function getItemTypeNameAttribute()
    // {
    //     return ItemType::select('type_name')->where('id', $this->item_type)->first();
    // }

    // public function getDateSequenceAttribute()
    // {
    //     $dateStatus = DateStatus::select('sequence')->where('id', $this->status)->first();

    //     return $dateStatus->sequence;
    // }

    // public function getFullNameAttribute()
    // {
    //     $userId = ItemHistory::select('author_idea')->where('item', $this->id)->first();
        
    //     $authorName = User::select('id', 'first_name', 'last_name')->where('id', $userId->author_idea)->first();
       
    //     return $authorName;
    // }

    // public function getNotesAttribute()
    // {
    //     $note = ItemHistory::select('notes')->where('item', $this->id)->first();

    //     return $note->notes;
    // }

}
