<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachments';
    protected $guarded = [];
    protected $appends = ['filename'];
    
    public function getFilenameAttribute(){
        $url_array = explode("/", $this->file_url);
        return end($url_array);
    }

    public function getDocumentTypeAttribute(){
        return $this->belongsTo(DocumentType::class);
    }

    public function getDocumentCategoryAttribute(){
        return $this->belongsTo(DocumentCategory::class);
    }
}
