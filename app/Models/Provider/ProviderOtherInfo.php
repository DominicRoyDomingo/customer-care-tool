<?php

namespace App\Models\Provider;

use Illuminate\Database\Eloquent\Model;
use App\Models\InformationType;

class ProviderOtherInfo extends Model
{
    protected $table = 'provider_other_info';
    protected $guarded = [];
    protected $appends = ['information_type'];

    public function type_item()
    {
        return $this->hasOne(InformationType::class, 'id', 'type_of_info');
    }

    public function getInformationTypeAttribute()
    {

        $informationType = InformationType::where('id', $this->type_of_info)->first();

        return $informationType;
    }
}
