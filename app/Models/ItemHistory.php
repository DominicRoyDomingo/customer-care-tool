<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Auth\User;

use App\Models\DateStatus;

use App\Models\ContentItem;

use App\Models\ItemType;



class ItemHistory extends Model
{
    protected $table = 'items_history';

    protected $guarded = [];

    public function author_task_object() {
        return $this->belongsTo(User::class, 'author_task');
    }

    public function author_idea_object() {
        return $this->belongsTo(User::class, 'author_idea');
    }
    
    public function getItemsAttribute()
    {
        $itemName = ContentItem::select('id', 'item_name', 'created_at', 'updated_at')->where('id', $this->attributes['item'])->first();

        return $itemName;
    }

    public function getAuthorTaskNameAttribute()
    {
        $authorName = User::select('id', 'first_name', 'last_name')->where('id', $this->attributes['author_task'])->first();

        return $authorName->full_name;
    }

    public function getAuthorIdeaNameAttribute()
    {
        $authorName = User::select('id', 'first_name', 'last_name')->where('id', $this->attributes['author_idea'])->first();

        return $authorName->full_name;
    }

    public function getStatusNameAttribute()
    {
        $dateStatus = DateStatus::select('status')->where('id', $this->attributes['status'])->first();

        return $dateStatus->status;
    }

    public function getAuthorIdAttribute()
    {
        // $userId = ItemHistory::select('author_idea')->where('item', $this->id)->first();
        
        $authorName = User::select('id', 'first_name', 'last_name')->where('id', $this->attributes['author_idea'])->first();

        return $authorName->id;
    }

    public function getItemDateSequenceAttribute()
    {
        $dateStatus = DateStatus::select('sequence')->where('id', $this->attributes['status'])->first();

        return $dateStatus->sequence;
    }

    public function getItemHistoryTypeNameAttribute()
    {
        $item = ContentItem::select('item_type')->where('id', $this->attributes['item'])->first();

        $itemType = ItemType::select('type_name')->where('id', $item->item_type)->first();

        return $itemType->type_name;
    }
}