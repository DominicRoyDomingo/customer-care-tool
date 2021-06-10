<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\PublishingItem;

use App\Models\LanguageValidator;

class PublishingItemController extends Controller
{
   
    public function index() {
        
        return view('backend.publishing.item.index');

    }


    public function getList( Request $request ) {
        
        $lang = $request -> input( 'lang' );

        $show_data = $this -> show_data( $lang );

        $publishing_item = array();

        foreach( $show_data as $datas ) {

            if( $datas[ 'name' ] != null ) {

                array_push( $publishing_item, [

                    'id' => $datas[ 'id' ],

                    'name' => $datas[ 'name' ],

                    'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),

                    'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) )

                ] );

            }

        }

        return response() -> json( $publishing_item );

    }


    public function postPublishingItem( Request $request ) {

        $input = json_decode( $request -> input( 'data' ) );

        $lang = $request -> input( 'lang' );

        $publishing_item_check = new PublishingItem;

        if ( $publishing_item_check -> count() > 0 ) {

            foreach ( $publishing_item_check -> get() as $publishing_item_checks ) {

                $publishing_item_checks_ = $this -> view_publishing_item( $publishing_item_checks, $lang );

                if(  ucwords( $publishing_item_checks_[ 'name' ] ) == ucwords( $input -> name ) ) {

                    $message = ucwords( string_to_value( $lang, $input -> name ) );

                    return response() -> json( alert_duplicate( $message, $input ) );

                }

            }

        }

        $formData = array(

            'name' => ucwords( $input -> name )

        );

        $jsonData = string_to_json( $lang, [ 'name' ], $formData );

        $publishing_item = $publishing_item_check -> create( $jsonData );

        if ( $publishing_item ) {

            $message = ucwords( string_to_value( $lang, $publishing_item[ 'name' ] ) );

            return response() -> json( alert_success( $message, $publishing_item ) );

        }

    }


    public function updatePublishingItem( Request $request ) {

        $formData = json_decode( $request -> input( 'data' ) );

        if( ! empty (  $formData -> id ) ) {

            $lang = $formData -> language;

            $publishing_item_check = new PublishingItem;

            if ( $publishing_item_check -> count() > 0 ) {

                foreach ( $publishing_item_check -> get() as $publishing_item_checks ) {

                    $publishing_item_checks_ = $this -> view_publishing_item( $publishing_item_checks, $lang );

                    if( ucwords( $publishing_item_checks_[ 'name' ] ) == ucwords( $formData -> name ) && $publishing_item_checks_[ 'id' ] != $formData -> id ) {

                        $message = ucwords( string_to_value( $lang, $formData -> name ) );

                        return response() -> json( alert_duplicate( $message, $formData ) );

                    }

                }

            }

            $respQuestion = $publishing_item_check -> changeData( $request );

            if ( $respQuestion ) {

                $message = ucwords( string_to_value( $lang, $respQuestion -> name));

                return response() -> json( alert_update( $message, $respQuestion ) );

            }

        }

    }
    

    public function deletePublishingItem( Request $request, $id ) {

        $lang = $request -> input( 'lang' );

        $publishing_item = PublishingItem::whereId( $id );

        $name = $publishing_item -> select('name') -> first() -> name;

        if ( $publishing_item -> delete() ) {

            $message = ucwords( string_to_value( $lang, $name ) );

            return response() -> json( alert_delete( $message ) );

        }

    }

    
    public function getPublishingItemName( Request $request, $id, $lang ) {

        $publishing_item = PublishingItem::whereId( $id ) -> first();

        $message = ucwords( string_to_value( $lang, $publishing_item -> name) );

        return response() -> json( [ 'name' => $message ] );

    }


    public function view_publishing_item( $data, $lang ) {

        return [

        'id' => $data -> id,

        'name' => string_to_value( $lang, $data -> name ),

        'created_at' => $data -> created_at,

        'updated_at' => $data -> updated_at,

        ];

    }

    
    public function show_data( $lang ) {

        $publishing_item = PublishingItem::selectRaw( "id, name, created_at, updated_at" ) -> get();

        $language_setting = new LanguageValidator;

        $json = $language_setting -> get_data( $lang  , [ 'id', 'name', 'created_at', 'updated_at' ], $publishing_item );

        return $json;

    }

}
