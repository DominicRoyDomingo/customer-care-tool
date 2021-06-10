<?php

namespace App\Observers\Terms;

use App\Models\MedicalStuff\MedicalArticle;
use DB;

class ArticleObserver
{
    public function created(MedicalArticle $article)
    {
        DB::beginTransaction();
        try {
            DB::table('brand_articles')->insert([
                'article_id' => $article->id,
                'brand_id' => request()->brand_id,
                'created_at' => now(),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
