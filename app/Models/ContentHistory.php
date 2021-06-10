<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Auth\User;

use App\Models\DateStatus;

use App\Models\Content;

class ContentHistory extends Model
{
    protected $table = 'content_history';

    protected $guarded = [];

    public function author_task_object() {
        return $this->belongsTo(User::class, 'author_task');
    }

    public function author_idea_object() {
        return $this->belongsTo(User::class, 'author_idea');
    }

    public function date_status() {

        return $this->belongsTo(DateStatus::class, 'status');

    }

    public function contents() {

        return $this->belongsTo(Content::class, 'content');

    }

    // public function getContentsAttribute()
    // {
    //     $content = Content::select('id', 'name', 'created_at', 'updated_at')->where('id', $this->attributes['content'])->first();

    //     return $content;
    // }

    public function getContentStatusNameAttribute()
    {
        $dateStatus = DateStatus::select('status')->where('id', $this->attributes['status'])->first();

        return $dateStatus->status;
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

    public function getContentDateSequenceAttribute()
    {
        $dateStatus = DateStatus::select('sequence')->where('id', $this->attributes['status'])->first();

        return $dateStatus->sequence;
    }

    public function getAuthorIdAttribute()
    {
        // $userId = ItemHistory::select('author_idea')->where('item', $this->id)->first();
        
        $authorName = User::select('id', 'first_name', 'last_name')->where('id', $this->attributes['author_idea'])->first();

        return $authorName->id;
    }

}
