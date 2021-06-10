<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;
class ClientJob extends Model
{
    //
    protected $guarded = [];
    protected $with = ['category', 'profession', 'description'];
    
    public function profile() {
        return $this->belongsTo(Profile::class);
    }
    
    public function profession() {
        return $this->belongsTo(JobProfession::class, "job_profession_id");
    }
    
    public function category() {
        return $this->belongsTo(JobCategory::class, "job_category_id");
    }
    
    public function description() {
        return $this->belongsTo(JobDescription::class, "job_description_id");
    }
}
