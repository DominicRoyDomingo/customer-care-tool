<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublishingItem extends Model
{
    protected $table = 'publishing_item';

    protected $guarded = [ 'id' ];

    protected $fillable = [ 'name' ];

    public function create( $data ) {

        $item = new PublishingItem;

        $item -> name = $data[ 'name' ];

        $item -> save();

        return $item;

    }

  
    public function changeData( $data ){

        $formData = json_decode( $data -> input( 'data' ) );

        $lang = $formData -> language;
        
        $item = PublishingItem::whereId( $formData -> id ) -> first();

        $typeVal = string_add_json( $lang, ucwords( $formData -> name ), string_remove( $lang, $item-> name ) );

        $item-> name = $typeVal;

        $item-> save();

        return $item;

    }

}