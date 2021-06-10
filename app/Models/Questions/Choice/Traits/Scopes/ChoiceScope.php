<?php

namespace App\Models\Questions\Choice\Traits\Scopes;

use DB;

/**
 * Terms Scope
 */
trait ChoiceScope
{

    public function scopeMedicalTerm($query, $id)
    {
        return $query->findOrFail($id);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query
            ->join('brand_terms', function ($join) {
                $join
                    ->on('medical_terms.id', '=', 'brand_terms.terminology_id')
                    ->where('brand_terms.brand_id', request()->brand_id);
            });
    }

    public function scopeSortedByNoLang($query, $value, $sort = 'desc')
    {
       if ($value == 'all') {
          return $query->orderBy('created_at',  $sort);
       }
       $value = '"' . $value . '":';
       return  $query->where('value', 'not like', "%{$value}%");
    }

    public function scopeFiltered($query)
    {
        $request = request();
        $json_key = "$.{$request->locale}";
        $string = "%{$request->filter}%";

        $query
            ->where(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.en'))"), 'like', strtolower($string))
            ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.de'))"), 'like', strtolower($string))
            ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.it'))"), 'like', strtolower($string));
        // ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.ph-bis'))"), 'like', strtolower($string))
        // ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.ph-fil'))"), 'like', strtolower($string));
    }
}
