<?php

namespace App\Models\Questionnaires;

use App\Models\Auth\User;
use App\Models\Questionnaires\Traits\Attributes\QuestionnaireAttribute;
use App\Models\Questionnaires\Traits\Methods\QuestionnaireMethod;
use App\Models\Questionnaires\Traits\Scopes\QuestionnaireScope;
use App\Models\Questions\QuestionType;
use Illuminate\Database\Eloquent\Model;
use App\Models\Questions\Question;

class Questionnaire extends Model
{
    use QuestionnaireAttribute, QuestionnaireMethod, QuestionnaireScope;

    protected $guarded = [];

    protected $appends = [
        'questionnaire_name',
        'base_name',
        'is_loading',
        'is_english',
        'is_italian',
        'is_german',
        'is_bisaya',
        'is_filipino',
    ];

    public function created_by()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    public function question_sequences()
    {
        return $this->belongsToMany(Question::class, 'questionnaire_content', 'questionnaire_id', 'question_id')
            ->withPivot(['sequence', 'id'])
            ->orderBy('sequence', 'asc');
    }
}
