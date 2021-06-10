<?php

namespace App\Models\PaymentPlan;

use App\Models\Brand\BrandPlan;
use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentPlan\Traits\Attributes\PaymentPlanAttribute;
use App\Models\PaymentPlan\Traits\Methods\PaymentPlanMethod;
use App\Models\PaymentPlan\Traits\Scopes\PaymentPlanScope;


class PaymentPlan extends Model
{
  use PaymentPlanAttribute, PaymentPlanMethod, PaymentPlanScope;

  protected $table = 'payment_plan';
    
  protected $guarded = [];

  protected $appends = [
      'paymentplan_name',
      'has_translation',
      'base_name',
      'is_loading',
      'is_english',
      'is_italian',
      'is_german',
      'is_bisaya',
      'is_filipino',
      'on_select_payment_plan',
      'base_description',
      'base_features',
  ];

  public function brand_paymentplan()
  {
      return $this->hasMany(BrandPlan::class, 'plan');
  }

}
