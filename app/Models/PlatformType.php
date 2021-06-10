<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformType extends Model
{
    
  protected $table = 'platform_type';

  protected $guarded = [ 'id' ];

  protected $fillable = [ 'name' ];
  protected $appends = ['is_loading', 'platform_type_name', 'is_english', 'is_italian', 'is_german'];

  public function create( $data ) {

      $platform = new PlatformType;

      $platform -> name = $data[ 'name' ];

      $platform -> save();

      return $platform;

  }


  public function changeData( $data ){

    $formData = json_decode( $data -> input( 'data' ) );

    $lang = $formData -> language;
    
    $platform = PlatformType::whereId( $formData -> id ) -> first();

    $platformVal = string_add_json( $lang, ucwords( $formData -> name ), string_remove( $lang, $platform -> name ) );

    $platform -> name = $platformVal;

    $platform -> save();

    return $platform;

  }


  public function getIsEnglishAttribute()
  {
     return string_to_value('en', $this->name);
  }

  public function getIsGermanAttribute()
  {
     return string_to_value('de', $this->name);
  }

  public function getIsItalianAttribute()
  {
     return string_to_value('it', $this->name);
  }

  public function getPlatformTypeNameAttribute()
  {
     $name = string_to_value(locale(), $this->name);

     $base_name = $this -> getIsEnglishAttribute();
      
     if (is_null($name)) {

        $mesage =  ' <small style="color:red">(No English translation yet)</small>';

        switch (locale()) {
           case 'it':
              $mesage = $base_name. ' <small style="color:red">(No Italian translation yet)</small>';
              break;
           case 'de':
              $mesage = $base_name. ' <small style="color:red">(No German translation yet)</small>';
              break;
        }

        $name = $mesage;
     }
     
     return $name;
  }
  public function getIsLoadingAttribute()
  {
     return false;
  }
}
