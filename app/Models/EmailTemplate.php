<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    //
    protected $guarded = [];
    protected $with = [];
    protected $appends = ["variables_display"];
    
    public function getVariablesAttribute($value){
        return json_decode($value);
    }
    
    public function getVariablesDisplayAttribute(){
        $variables = $this->variables;
        $variables_display = [];

        foreach($variables as $variable){
            $variables_display[] = $variable->variable_name;
        }

        return implode(", ", $variables_display);
    }
}
