<?php

namespace App\Models\Questions;

use App\Models\Questions\Choice\Choice;
use App\Models\Questions\Question;
use Illuminate\Database\Eloquent\Model;

class QuestionLink extends Model
{
    protected $table = 'question_links';

    protected $guarded = [];

    protected $fillable = [
        'question',
        'choice',
        'succeeding',
        'iscorrect',
    ];

    public function choice()
    {
        return $this->hasOne(Choice::class, 'id', 'choice');
    }
}
