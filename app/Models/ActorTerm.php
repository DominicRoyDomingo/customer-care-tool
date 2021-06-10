<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActorTerm extends Model
{
    protected $table = 'actor_terms';

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
