<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\ItemType;

use App\Models\LanguageValidator;

use Illuminate\Support\Facades\DB;

class ItemTypeController extends Controller
{
    
    public function index() {
        
        return view('backend.item-type.index');

    }


    public function getList( Request $request ) {
        
        $lang = $request -> input( 'lang' );

        $language = 'English';

        if( $lang == 'it') { $language = 'Italian'; } 
        if( $lang == 'de' ) { $language = 'German'; }
        if( $lang == 'ph-fil') { $language = 'Filipino'; }
        if( $lang == 'ph-bis') { $language = 'Visayan'; }
        
        $get_publishing_type = ItemType::orderBy( 'id', 'DESC' ) -> get();
        
        $publishing_type = array();

        foreach( $get_publishing_type as $datas ) { 
            $base_name = $this -> getName( $datas[ 'id' ], $lang );
            $notif = '';
            if($base_name == ''){
                 $langs = ['it', 'de', 'ph-fil', 'ph-bis'];
                 $notif = " <i class='text-danger ml-1' style='font-size:10px'>(No {$language} translation yet)</i>";
                 $base_name = $this -> getName( $datas[ 'id' ], 'en' );
                 if($base_name == ''){
                    foreach($langs as $l){
                        $base_name = $this -> getName( $datas[ 'id' ], $l );
                        if($base_name != ''){
                            break;
                        }
                    }    
                 }
            }
            
            array_push( $publishing_type, [

                'id' => $datas[ 'id' ],

                'type_name' => $base_name . $notif,

                'base_name' => $base_name,
                
                'created_at' => date( 'm/d/Y', strtotime( $datas[ 'created_at' ] ) ),

                'updated_at' => date( 'm/d/Y', strtotime( $datas[ 'updated_at' ] ) ),

                'convertion' => ( ! empty( $name ) ? false : true ),

                'language' => $language

            ] );

        }

        return response() -> json( $publishing_type );

    }


    public function postItemType( Request $request ) {

        $input = json_decode( $request -> input( 'data' ) );

        $lang = $request -> input( 'lang' );

        $publishing_type_check = new ItemType;

        if ( $publishing_type_check -> count() > 0 ) {

            foreach ( $publishing_type_check -> get() as $publishing_type_checks ) {

                $publishing_type_checks_ = $this -> view_item_type( $publishing_type_checks, $lang );

                if(  ucwords( $publishing_type_checks_[ 'type_name' ] ) == ucwords( $input -> type_name ) ) {

                    $message = ucwords( string_to_value( $lang, $input -> type_name ) );

                    return response() -> json( alert_duplicate( $message, $input ) );

                }

            }

        }

        $formData = array(

            'type_name' => ucwords( $input -> type_name )

        );

        $jsonData = string_to_json( $lang, [ 'type_name' ], $formData );

        $publishing_type = $publishing_type_check -> create( $jsonData );

        if ( $publishing_type ) {

            $message = ucwords( string_to_value( $lang, $publishing_type[ 'type_name' ] ) );

            return response() -> json( alert_success( $message, $publishing_type ) );

        }

    }

    public function searchItemType( Request $request ){

        $search = $request->search;
        $lang = $request -> input( 'lang' );
        $language = 'English';

        if( $lang == 'it') { $language = 'Italian'; } 
        if( $lang == 'de' ) { $language = 'German'; }
        if( $lang == 'ph-fil') { $language = 'Filipino'; }
        if( $lang == 'ph-bis') { $language = 'Visayan'; }

        $find = ItemType::where('type_name', 'like', '%' . $search . '%')->get();

        if($find->count()){
            $find_arr = [];
            foreach($find as $f){                
                $res = $this->view_item_type( $f, $lang );
                $base_name = $this -> getName( $f[ 'id' ], $lang );
                $notif = '';
                if($base_name == ''){
                     $langs = ['it', 'de', 'ph-fil', 'ph-bis'];
                     $notif = " <i class='text-danger ml-1' style='font-size:10px'>(No {$language} translation yet)</i>";
                     $base_name = $this -> getName( $f[ 'id' ], 'en' );
                     if($base_name == ''){
                        foreach($langs as $l){
                            $base_name = $this -> getName( $f[ 'id' ], $l );
                            if($base_name != ''){
                                break;
                            }
                        }    
                     }
                }

                $res['type_name'] = $base_name . $notif;
                $res['base_name'] = $base_name;
                $res['created_at'] = date("m/d/Y", strtotime( $res[ 'created_at' ] ));
                $res['updated_at'] = date("m/d/Y", strtotime( $res[ 'updated_at' ] ));
                $find_arr[] =  $res;

               
            }

            return response() -> json($find_arr);
        }

        return response() -> json(false);
    }

    public function sortItemType( Request $request ){

        $sortcol = $request->sortcol;
        $search = $request->filter;
        $sort = ( $request->sort == true ) ? 'desc' : 'asc';
        $lang = $request -> input( 'lang' );

        $find = ItemType::where('type_name', 'like', '%' . $search . '%')->get();

        if($sortcol == 'index'){
            $find = ItemType::where('type_name', 'like', '%' . $search . '%')->orderBy('id', $sort)->get();
        }
        else if($sortcol == 'term_type_name'){
            $find = ItemType::where('type_name', 'like', '%' . $search . '%')->orderBy('type_name', $sort)->get();
        }
        else if($sortcol == 'created_at'){
            $find = ItemType::where('type_name', 'like', '%' . $search . '%')->orderBy('created_at', $sort)->get();
        }

        $language = 'English';
        if( $lang == 'it') { $language = 'Italian'; } 
        if( $lang == 'de' ) { $language = 'German'; }
        if( $lang == 'ph-fil') { $language = 'Filipino'; }
        if( $lang == 'ph-bis') { $language = 'Visayan'; }

        if($find->count()){
            $find_arr = [];
            foreach($find as $f){
                
                $res = $this->view_item_type( $f, $lang );
                $base_name = $this -> getName($f[ 'id' ], $lang );
                $notif = '';
                if($base_name == ''){
                     $langs = ['it', 'de', 'ph-fil', 'ph-bis'];
                     $notif = " <i class='text-danger ml-1' style='font-size:10px'>(No {$language} translation yet)</i>";
                     $base_name = $this -> getName( $f[ 'id' ], 'en' );
                     if($base_name == ''){
                        foreach($langs as $l){
                            $base_name = $this -> getName( $f[ 'id' ], $l );
                            if($base_name != ''){
                                break;
                            }
                        }    
                     }
                }

                $res['type_name'] = $base_name . $notif;
                $res['base_name'] = $base_name;
                $res['created_at'] = date("m/d/Y", strtotime( $res[ 'created_at' ] ));
                $res['updated_at'] = date("m/d/Y", strtotime( $res[ 'updated_at' ] ));
                $find_arr[] =  $res;               
            }


            return response() -> json($find_arr);
        }

        return response() -> json(false);
    }


    public function updateItemType( Request $request ) {

        $formData = json_decode( $request -> input( 'data' ) );

        if( ! empty (  $formData -> id ) ) {

            $lang = $formData -> language;

            $publishing_type_check = new ItemType;

            if ( $publishing_type_check -> count() > 0 ) {

                foreach ( $publishing_type_check -> get() as $publishing_type_checks ) {

                    $publishing_type_checks_ = $this -> view_item_type( $publishing_type_checks, $lang );

                    if( ucwords( $publishing_type_checks_[ 'type_name' ] ) == ucwords( $formData -> type_name ) && $publishing_type_checks_[ 'id' ] != $formData -> id ) {

                        $message = ucwords( string_to_value( $lang, $formData -> type_name ) );

                        return response() -> json( alert_duplicate( $message, $formData ) );

                    }

                }

            }

            $respQuestion = $publishing_type_check -> changeData( $request );

            if ( $respQuestion ) {

                $message = ucwords( string_to_value( $lang, $respQuestion -> type_name));

                return response() -> json( alert_update( $message, $respQuestion ) );

            }

        }

    }
    

    public function deleteItemType( Request $request, $id ) {

        $lang = $request -> input( 'lang' );

        $publishing_type = ItemType::whereId( $id );

        $links = DB::table('publishing_item_type_link')->where('publishing_item_type_id', $id)->get();

        if($links->count()){
            $type_name = $publishing_type -> select('type_name') -> first() -> type_name;

            $message = ucwords( string_to_value( $lang, $type_name));

            return response() -> json( alert_failed_delete( $message, [])); 
        }

        $type_name = $publishing_type -> select('type_name') -> first() -> type_name;

        if ( $publishing_type -> delete() ) {

            $message = ucwords( string_to_value( $lang, $type_name ) );

            if(empty($message) || is_null($message)){

                $langs = ['en', 'it', 'de', 'ph-fil', 'ph-bis'];

                foreach($langs as $l){
                    $message = ucwords( string_to_value(  $l, $type_name ) );                    
                    if($message != ''){
                        break;
                    }
                }

                return response() -> json( alert_delete( $message ) );
            }

            return response() -> json( alert_delete( $message ) );

        }

    }

    
    public function getItemTypeName( Request $request, $id, $lang ) {

        $publishing_type = ItemType::whereId( $id ) -> first();

        $message = ucwords( string_to_value( $lang, $publishing_type -> type_name) );

        return response() -> json( [ 'type_name' => $message ] );

    }


    public function view_item_type( $data, $lang ) {

        return [

        'id' => $data -> id,

        'type_name' => string_to_value( $lang, $data -> type_name ),

        'created_at' => $data -> created_at,

        'updated_at' => $data -> updated_at,

        ];

    }

    
    public function show_data( $lang ) {

        $publishing_type = ItemType::selectRaw( "id, type_name, created_at, updated_at" ) -> get();

        $language_setting = new LanguageValidator;

        $json = $language_setting -> get_data( $lang  , [ 'id', 'type_name', 'created_at', 'updated_at' ], $publishing_type );

        return $json;

    }

    public function getName( $id, $lang ) {
        
        $tags = ItemType::whereId( $id ) -> first();

        $name = ucwords( string_to_value( $lang, $tags -> type_name) );

        return $name;

    }

}
