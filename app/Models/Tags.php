<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
  
  protected $table = 'tags';

  protected $guarded = [ 'id' ];

  protected $fillable = [ 'name' ];

  public function create( $data ) {

      $tags = new Tags;

      $tags -> name = $data[ 'name' ];

      $tags -> save();

      return $tags;

  }

  
  public function changeData( $data ){

    $formData = json_decode( $data -> input( 'data' ) );
    
    $lang = $formData -> language;
    
    $tags = Tags::whereId( $formData -> id ) -> first();

    $tagsVal = string_add_json( $lang, ucwords( $formData -> name ), string_remove( $lang, $tags -> name ) );
    
    $tags -> name = $tagsVal;

    $tags -> save();

    return $tags;

  }

}
