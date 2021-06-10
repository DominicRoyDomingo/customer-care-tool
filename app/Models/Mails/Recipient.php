<?php

namespace App\Models\Mails;

use App\Models\Auth\User;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
   protected $fillable = ['recipient', 'campaign_email_id'];


   public function profile()
   {
      return $this->belongsTo(Profile::class, 'recipient');
   }
}
