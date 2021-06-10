<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Actor;
use App\Models\PhysicalCodeType;

class ValidateIdentificationCode implements Rule
{
    public $brandId;
    public $actorId;
    public $error;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($brandId,$actorId)
    {
        $this->brandId = $brandId;
        $this->actorId = $actorId;
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
        // dd($this->actorId);
        $actors = Actor::select('id', 'physical_code')->with([
            'actors_brand_item',
        ]);

        $actors = $actors->where(function ($subQuery) {
            $subQuery->whereHas('actors_brand_item', function ($query) {
                $query->where('brands',  $this->brandId);
            });
        });
        $actors = $actors->get();

        $existingInformations = array();
        $informations = array();
        foreach ($value as $information) {
            unset($information['index']);
            array_push($informations, $information);
        }

        foreach( $informations as $information ) {
            if( !in_array( $information['code'], $existingInformations )) {
                $existingInformations[]    = $information['code'];
            }
            else{
                
                $this->error = $information['code'];
                return false;

            }
        }
   
        foreach ($actors as $actor) {
            if($actor->id != $this->actorId){
                if($actor->physical_code != null) {
                    $actorInformations = array();
                    $existingActorInformations = array();
                    $rawActorInformations = json_decode($actor->physical_code, true);
        
                    foreach ($rawActorInformations as $actorInformation) {
                        unset($actorInformation['index']);
                        array_push($actorInformations, $actorInformation);
                    }

                    $newActorInformations = array_merge($actorInformations,$informations);

                    foreach( $newActorInformations as $information ) {
                    
                        if( !in_array( implode("",$information), $existingActorInformations )) {
                            $existingActorInformations[]    = implode("",$information);
                        }
                        else{
                            // dd(locale());
                            $physicalCodeType = PhysicalCodeType::where('id', $information['physical_code_type'])->first();

                            $this->error = $physicalCodeType->physical_code_type_name;

                            return false;
                        }
                    }
                }
            } 
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->error.' is an existing physical code record.';
    }
}
