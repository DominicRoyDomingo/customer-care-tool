<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\DateStatus;

use App\Models\Auth\User;

use App\Models\ContentItem;

class Content extends Model
{
    protected $table = 'content';

    protected $guarded = [];
    
    public function changeData( $data ){

        $formData = json_decode( $data -> input( 'data' ) );

        $lang = $formData -> language;
        
        $content = Content::whereId( $formData -> id ) -> first();

        $contentVal = string_add_json( $lang, ucwords( $formData -> name ), string_remove( $lang, $content -> name ) );

        $content -> name = $contentVal;

        $content -> save();

        return $content;

    }

    public function content_history() {

        return $this->hasOne(ContentHistory::class, 'content', 'id')->latest();

    }

    public function date_status() {

        return $this->belongsTo(DateStatus::class, 'status');
        
    }

    public function items() {

        return $this->hasMany(ContentItem::class, 'content', 'id');
        
    }

    public function getDateSequenceAttribute()
    {
        $dateStatus = DateStatus::select('sequence')->where('id', $this->status)->first();

        return $dateStatus->sequence;
    }

    // public function getItemsAttribute()
    // {
    //     $itemsCount = Item::select('id', 'item_name')->where('content', $this->id)->count();

    //     return $itemsCount;
    // }

    public function getFullNameAttribute()
    {
        $userId = ContentHistory::select('author_idea')->where('content', $this->id)->first();
        
        $authorName = User::select('id', 'first_name', 'last_name')->where('id', $userId->author_idea)->first();

        return $authorName;
    }

    public function getNotesAttribute()
    {
        $note = ContentHistory::select('notes')->where('content', $this->id)->first();
        
        return $note->notes;
    }

    public function getDateStatusNameAttribute()
    {
        return DateStatus::select('status')->where('id', $this->status)->first()->status;
    }

}