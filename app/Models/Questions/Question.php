<?php

namespace App\Models\Questions;

use App\Models\Brand;
use App\Models\Questions\Choice\Traits\Attributes\QuestionAttribute;
use App\Models\Questions\Choice\Traits\Methods\QuestionMethod;
use App\Models\Questions\QuestionLink;
use App\Models\Questions\Traits\ExtendedEloquentTrait;
use App\Models\Questions\Traits\Scopes\QuestionScope;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use QuestionAttribute, QuestionMethod, ExtendedEloquentTrait, QuestionScope;

    protected $table = 'questions';

    protected $guarded = [];

    protected $appends = [
        'question_name',
        'base_name',
        'is_loading',
        'is_english',
        'is_italian',
        'is_german',
        'is_bisaya',
        'is_filipino',
        'translated_question',
        'locale_question'
    ];

    public function question_type()
    {
        return $this->belongsTo(QuestionType::class, 'type');
    }

    public function question_links()
    {
        return $this->hasMany(QuestionLink::class, 'question');
    }

    /**
     * The questions that belong to the brand.
     */
    public function brand()
    {
        return $this->belongsToMany(Brand::class, 'brand_questions', 'questions', 'brand');
    }
}
