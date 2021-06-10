<?php

namespace App\Models\Questions;

use App\Models\Questions\Traits\Attributes\QuestionTypeAttribute;
use App\Models\Questions\Traits\Methods\QuestionTypeMethod;
use App\Models\Questions\Traits\Scopes\QuestionTypeScope;
use Illuminate\Database\Eloquent\Model;

class QuestionType extends Model
{
  use QuestionTypeAttribute, QuestionTypeMethod, QuestionTypeScope;
	/**
	 * table name
	 * @var string
	 */
	protected $table = 'question_types';

	/**
	 * Fillable columns
	 * @var array
	 */
    protected $fillable = [
      'name',
      'type_of_data'
   	];

   	/**
   	 * Appen attribute or column
   	 * @var array
   	 */
   	protected $appends = [
      'translated_name',
      'locale_name',
      'question_type_name',
      'base_name',
      'is_loading',
      'is_english',
      'is_italian',
      'is_german',
      'is_bisaya',
      'is_filipino',
      'on_select_question_type_name'
    ];

   	/**
   	 * Guarded column
   	 * @var array
   	 */
   	protected $guarded = ['id'];

   	
}
