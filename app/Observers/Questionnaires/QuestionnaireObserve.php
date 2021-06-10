<?php

namespace App\Observers\Questionnaires;

use App\Models\MedicalStuff\MedicalArticle;
use App\Models\Questionnaires\Questionnaire;
use DB;

class QuestionnaireObserve
{
    public function created(Questionnaire $q)
    {
        DB::beginTransaction();
        try {
            DB::table('brand_quesionnaires')
                ->insert([
                    'questionnaire_id' => $q->id,
                    'brand_id' => request()->brand_id,
                    'created_at' => now(),
                ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
