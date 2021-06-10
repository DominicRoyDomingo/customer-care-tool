<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticsExtension extends Model
{
    //
    protected $guarded = [];
    
    public function field(){
        return $this->belongsTo(StatisticsField::class, 'statistics_field_id');
    }
}
