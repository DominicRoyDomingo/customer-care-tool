<?php

namespace App\Models\Questions\Choice;

use Illuminate\Database\Eloquent\Model;
use App\Models\Questions\Choice\Traits\Attributes\ChoiceAttribute;
use App\Models\Questions\Choice\Traits\Methods\ChoiceMethod;
use App\Models\Questions\Choice\Traits\Scopes\ChoiceScope;


class Choice extends Model
{
  use ChoiceAttribute, ChoiceMethod, ChoiceScope;

  protected $table = 'choices';
    
  protected $guarded = [];

  protected $appends = [
      'choice_name',
      'has_translation',
      'base_name',
      'is_loading',
      'is_english',
      'is_italian',
      'is_german',
      'is_bisaya',
      'is_filipino',
      'on_select_choice_name'
  ];

  public function questionLinks()
  {
      return $this->hasMany(QuestionLink::class, 'choice');
  }


}
