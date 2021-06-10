<?php

namespace App\Observers\Terms;

use App\Models\MedicalStuff\MedicalTerm;
use DB;

class TermObserver
{
    public function created(MedicalTerm $term)
    {
        DB::beginTransaction();
        try {
            DB::table('brand_terms')->insert([
                'terminology_id' => $term->id,
                'brand_id' => request()->brand_id,
                'created_at' => now(),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
