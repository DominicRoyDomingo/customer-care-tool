<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Actor;

class ValidateActor implements Rule
{
    public $firstname;
    public $middlename;
    public $id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($surname,$firstname,$middlename,$id)
    {
        $this->surname = $surname;
        $this->firstname = $firstname;
        $this->middlename = $middlename;
        $this->id = $id;
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $isActorExists = !Actor::where('surname', $value)
        ->when($this->firstname != null, function ($q) {
            return $q->where('firstname', $this->firstname);
        })
        ->when($this->middlename != null, function ($q) {
            return $q->where('middlename', $this->middlename);
        })->exists();
        if($this->id != null) {
            
            if(Actor::where('id',$this->id)
            ->where('surname', $value)->where('firstname', $this->firstname)->where('middlename',$this->middlename)->exists()) {
                return true;
            }
            return $isActorExists;
        }
        
        return $isActorExists;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $fullname = $this->fullname();
        return $fullname. ' is an existing actor record.';
    }

    public function fullname() {
        if($this->firstname == "") {
            return $this->surname;
        }
        
        $name =  $this->surname . ", " . $this->firstname;
        if ($this->middlename != "")
            $name .= " " . ucfirst(substr($this->middlename, 0, 1)) . ".";
        return $name;
    }
}
