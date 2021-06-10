<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;

class Contact extends Model
{
    protected $guarded = [];
    protected $with = ["contact_type"];
    protected $appends = ["added_by", "contact_info_display"];

    public function profile(){
        return $this->belongsTo(Profile::class);
    }

    public function contact_type(){
        return $this->belongsTo(ContactType::class);
    }

    public function getContactInfoDisplayAttribute(){
        return $this->contact_info;
    }

    public function adder(){
        $user = $this->belongsTo(User::class, "created_by");
        return $user;
    }

    public function getAddedByAttribute(){
        $user = $this->adder;
        $user = User::findOrfail($user->id);
        return $user->full_name();
    }

    public function created_at_display(){
        return date("m/d/Y", strtotime($this->created_at));
    }
}
