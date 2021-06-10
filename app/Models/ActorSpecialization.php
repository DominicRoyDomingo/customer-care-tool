<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Jobs\JobDescription;
use App\Models\Jobs\JobProfession;
use App\Models\Jobs\JobCategory;

class ActorSpecialization extends Model
{
    protected $table = 'actor_specialization';

    protected $guarded = [];

    public function category_item() {
        return $this->hasOne(JobCategory::class, 'id', 'category');
    }

    public function profession_item() {
        return $this->hasOne(JobProfession::class, 'id', 'profession');
    }

    public function specialization_item() {
        return $this->hasOne(JobDescription::class, 'id', 'specialization');
    }


}
