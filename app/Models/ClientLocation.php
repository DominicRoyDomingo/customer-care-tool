<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Khsing\World\World;
use Khsing\World\Models\Country;
use Khsing\World\Models\Division;
use Khsing\World\Models\City;

class ClientLocation extends Model
{
    //
    protected $guarded = [];
    
    public function profile() {
        return $this->belongsTo(Profile::class);
    }

    public function city() {
        return $this->belongsTo(City::class, "world_cities_id");
    }
    
    public function province() {
        return $this->belongsTo(Division::class, "world_provinces_id");
    }
    
    public function country() {
        return $this->belongsTo(Country::class, "world_countries_id");
    }

    public function getLocationDisplayAttribute(){
        $location = "";
        
        if(isset($this->city)){
            if($location != ""){
                $location .= ", ";
            }
            $location .= $this->city->name;
        }

        if(isset($this->province)){
            if($location != ""){
                $location .= ", ";
            }

            $location .= $this->province->name;
        }

        if(isset($this->country)){
            if($location != ""){
                $location .= ", ";
            }

            $location .= $this->country->name;
        }

        if($location == ""){
            $location = "N/A";
        }
        
        return $location;
    }
}
