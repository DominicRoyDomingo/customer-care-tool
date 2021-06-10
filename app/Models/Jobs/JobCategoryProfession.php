<?php

namespace App\Models\Jobs;


use App\Models\Jobs\JobCategory;
use App\Models\Jobs\JobProfession;
use Illuminate\Database\Eloquent\Model;

class JobCategoryProfession extends Model
{
   protected $table = 'job_category_profession';

   protected $fillable = ['job_category_id', 'job_profession_id'];

   public function jobCategory()
   {
      return $this->belongsTo(JobCategory::class, 'job_category_id');
   }

   public function jobProfession()
   {
      return $this->belongsTo(JobProfession::class, 'job_profession_id');
   }
}
