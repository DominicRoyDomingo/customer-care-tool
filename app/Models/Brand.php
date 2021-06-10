<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Actions\Engagement;
use App\Models\MedicalStuff\MedicalTerm;
use App\Models\Brand\BrandCategory;

class Brand extends Model
{
    protected $table = 'brands';

    protected $guarded = [];

    protected $appends = [
        'is_loading',
        'brand_name',
        'created_at_display',
        'updated_at_display',
        'created_at_order',
        'updated_at_order',
        'on_select_brand_name',
        'base_name'
    ];

    public function getIsLoadingAttribute()
    {
        return false;
    }
    public function brand_clients()
    {
        return $this->hasMany(BrandClient::class);
    }

    public function brand_categories()
    {
        return $this->hasMany(BrandCategory::class, 'brand', 'id');
    }

    public function getBrandEngagementsAttribute()
    {
        $engagements = Engagement::all();
        $brand_engagements = 0;

        foreach ($engagements as $engagement) {
            $brands = json_decode($engagement->brands);
            if ($brands != null) {
                if (in_array($this->id, $brands)) {
                    $brand_engagements++;
                }
            }
        }

        return $brand_engagements;
    }

    public function categories()
    {
        return $this->hasMany(CategoryBrand::class);
    }

    public function brand_platforms()
    {
        return $this->hasMany(PlatformItem::class, 'brand');
    }

    // public function request_type()
    // {
    //     return $this->hasMany(RequestType::class, 'brands');
    // }

    public function getBrandNameAttribute()
    {
        return $this->name;
    }

    public function getCreatedAtDisplayAttribute()
    {
        return date("F d, Y", strtotime($this->created_at));
    }

    public function getUpdatedAtDisplayAttribute()
    {
        return date("F d, Y", strtotime($this->updated_at));
    }

    public function getCreatedAtOrderAttribute()
    {
        return date("Ymd", strtotime($this->created_at));
    }

    public function getUpdatedAtOrderAttribute()
    {
        return date("Ymd", strtotime($this->updated_at));
    }

    public function termonilogies()
    {
        return $this->belongsToMany(MedicalTerm::class, 'brand_terms', 'brand_id', 'terminology_id');
    }

    public function getOnSelectBrandNameAttribute()
    {
      return $this->brand_name;
    }

    public function getBaseNameAttribute()
    {
        return $this->get_base_name();
    }

    public function base_name()
    {
        $baseName = $this->is_english;
        if (locale() === 'it')  $baseName = $this->is_italian;
        if (locale() === 'de')  $baseName = $this->is_german;
        if (locale() === 'ph-fil')  $baseName = $this->is_filipino;
        if (locale() === 'ph-bis')  $baseName = $this->is_bisaya;

        return $baseName;
    }

    public function get_base_name($key = false)
    {
        $baseName = $this->base_name();
        
        if (!$baseName) {
            $priorityBaseName = $this->is_english ?? $this->is_italian;
            $priorityBaseName = $priorityBaseName ?? $this->is_german;
            $priorityBaseName = $priorityBaseName ?? $this->is_filipino;
            $baseName .= $priorityBaseName ?? $this->is_biasya;

            if ($key) {
                $language = language();
                $baseName .= "<i class='text-danger ml-1' style='font-size:10px'>(No {$language} translation yet)</i>";
            }
        }

        return $baseName;
    }
}
