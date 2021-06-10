<?php

namespace App\Observers\Terms;

use App\Models\MedicalStuff\MedicalTermType;
use DB;

class TypeObserver
{
    public function created(MedicalTermType $type)
    {
        DB::beginTransaction();
        try {
            DB::table('brand_term_types')->insert([
                'term_type_id' => $type->id,
                'brand_id' => request()->brand_id,
                'created_at' => now(),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
