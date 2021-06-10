<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\LanguageValidator;

use App\Models\DateStatus;

class DateStatusController extends Controller
{
    public function index()
    {

        return view('backend.dates.index');

    }

    public function getList(Request $request) 
    {

        $lang = $request -> input( 'lang' );
        
        $language = 'English';

        if( $lang == 'it') { $language = 'Italian'; } if( $lang == 'de' ) { $language = 'German'; }

        $getDateStatusesForPublishing = DateStatus::select('id','status','sequence','class','organization')->where('class', 'publishing')->orderBy( 'sequence', 'ASC' ) -> get();
        
        $getDateStatusesForPublishing = $this->byOrganization($getDateStatusesForPublishing);
        
        $dateStatusesForPublishing = array();

        foreach( $getDateStatusesForPublishing as $datas ) {
            
            $statusName = $this -> getStatusName( $datas->id, $lang );

            $baseStatusName = ( ! empty( $statusName ) ? $statusName : $this -> getStatusName( $datas[ 'id' ], 'en' ) );

            if( $statusName == '' && $baseStatusName == '' ) {

                $baseStatusNameItaly = $this -> getStatusName( $datas[ 'id' ], 'it' );

                if( $baseStatusNameItaly !== '' ) {

                    $baseStatusName = $baseStatusNameItaly;

                } else {

                    $baseStatusName = $this -> getStatusName( $datas[ 'id' ], 'de' );

                }
                
            }

            array_push( $dateStatusesForPublishing, [

                'id' => $datas->id,

                'status' => $baseStatusName,

                'sequence' => $datas->sequence,

                'class' => $datas->class,

                'convertion' => ( ! empty( $statusName ) ? false : true ),

                'language' => $language

            ] );

        }


        $getDateStatusesForItem = DateStatus::select('id','status','sequence','class','organization')->where('class', 'item')->orderBy( 'sequence', 'ASC' ) -> get();

        $getDateStatusesForItem = $this->byOrganization($getDateStatusesForItem);

        $dateStatusesForItem = array();

        foreach( $getDateStatusesForItem as $datas ) {
            
            $statusName = $this -> getStatusName( $datas->id, $lang );

            $baseStatusName = ( ! empty( $statusName ) ? $statusName : $this -> getStatusName( $datas[ 'id' ], 'en' ) );

            if( $statusName == '' && $baseStatusName == '' ) {

                $baseStatusNameItaly = $this -> getStatusName( $datas[ 'id' ], 'it' );

                if( $baseStatusNameItaly !== '' ) {

                    $baseStatusName = $baseStatusNameItaly;

                } else {

                    $baseStatusName = $this -> getStatusName( $datas[ 'id' ], 'de' );

                }
                
            }

            array_push( $dateStatusesForItem, [

                'id' => $datas->id,

                'status' => $baseStatusName,

                'sequence' => $datas->sequence,

                'class' => $datas->class,

                'convertion' => ( ! empty( $statusName ) ? false : true ),

                'language' => $language

            ] );

        }

        $getDateStatusesForContent = DateStatus::select('id','status','sequence','class','organization')->where('class', 'content')->orderBy( 'sequence', 'ASC' ) -> get();

        $getDateStatusesForContent = $this->byOrganization($getDateStatusesForContent);

        $dateStatusesForContent = array();

        foreach( $getDateStatusesForContent as $datas ) {
            
            $statusName = $this -> getStatusName( $datas->id, $lang );

            $baseStatusName = ( ! empty( $statusName ) ? $statusName : $this -> getStatusName( $datas[ 'id' ], 'en' ) );

            if( $statusName == '' && $baseStatusName == '' ) {

                $baseStatusNameItaly = $this -> getStatusName( $datas[ 'id' ], 'it' );

                if( $baseStatusNameItaly !== '' ) {

                    $baseStatusName = $baseStatusNameItaly;

                } else {

                    $baseStatusName = $this -> getStatusName( $datas[ 'id' ], 'de' );

                }
                
            }
            
            array_push( $dateStatusesForContent, [

                'id' => $datas->id,

                'status' => $baseStatusName,

                'sequence' => $datas->sequence,

                'class' => $datas->class,

                'convertion' => ( ! empty( $statusName ) ? false : true ),

                'language' => $language

            ] );

        }

        return response() -> json( [ 'dateStatusesForPublishing' => $dateStatusesForPublishing, 'dateStatusesForItem' => $dateStatusesForItem, 'dateStatusesForContent' => $dateStatusesForContent ]  );

    }


    public function getDateStatus(Request $request) 
    {

        $lang = $request -> input( 'lang' );
        
        
        $language = 'English';

        if( $lang == 'it') { $language = 'Italian'; } if( $lang == 'de' ) { $language = 'German'; }

        $getDateStatuses = DateStatus::select('id','status','sequence','class', 'organization')->where('class', $request->class )->orderBy( 'sequence', 'ASC' ) -> get();
       
        $getDateStatuses = $this->byOrganization($getDateStatuses);

        $items = array();

        foreach( $getDateStatuses as $datas ) {
            
            $statusName = $this -> getStatusName( $datas->id, $lang );

            $baseStatusName = ( ! empty( $statusName ) ? $statusName : $this -> getStatusName( $datas[ 'id' ], 'en' ) );

            if( $statusName == '' && $baseStatusName == '' ) {

                $baseStatusNameItaly = $this -> getStatusName( $datas[ 'id' ], 'it' );

                if( $baseStatusNameItaly !== '' ) {

                    $baseStatusName = $baseStatusNameItaly;

                } else {

                    $baseStatusName = $this -> getStatusName( $datas[ 'id' ], 'de' );

                }
                
            }

            array_push( $items, [

                'id' => $datas->id,

                'status' => $baseStatusName,

                'sequence' => $datas->sequence,

                'class' => $datas->class,

                'convertion' => ( ! empty( $statusName ) ? false : true ),

                'language' => $language

            ] );

        }
        return response() -> json( [ 'items' => $items ]  );

    }

    public function changeOrder( Request $request ) 
    {
        $sequence = 1;
        foreach($request->all() as $row)
        {
            DateStatus::find($row['id'])->update([
                'sequence' => $sequence++
            ]);
        }

        return response() -> json( 'TEST'  );

    }

    public function store( Request $request ) 
    {

        $request->validate([
                "status" => "required",
                "class" => "required",
            ],
            [
                'status.required' => 'Status is required.',
                'class.required' => 'Class is required.',
            ]
        );

        $en = ucwords($request->status);

        $datestatus = DateStatus::create([
            "status"        => json_encode([
                            "en" => $en,
                            ]),
            "class"         => $request->class,
            "organization"  => \Session::get('organization')
        ]);
        
        $previousDateStatus = DateStatus::where('id', '<', $datestatus->id)->where('class', $request->class)->where('organization', \Session::get('organization'))->orderBy('sequence','desc')->first();

        if($previousDateStatus != null) {

            $sequence = $previousDateStatus->sequence + 1;

        } else {
            
            $sequence = 1;
        }

        DateStatus::where('id', $datestatus->id)->update(array('sequence' => $sequence));

        return response() -> json( alert_success( $request->status ) );

    }

    public function update( Request $request ) 
    {

        $lang = ($request->language);

        $dateStatusId = $request->id;

        $request->validate([
                "status" => "required",
                "class" => "required",
            ],
            [
                'status.required' => 'Status is required.',
                'sequence.numeric' => 'Number only.',
                'class.required' => 'Class is required.',
            ]
        );

        $dateStatus = DateStatus::findOrFail($dateStatusId);

        $statusVal = string_add_json( $lang, ucwords( $request -> status ), string_remove( $lang, $dateStatus -> status ) );

        $dateStatus->update([
            "status"    => $statusVal,
            "class"     => $request->class
        ]);

        return response() -> json( alert_update( $request->status ) );

    }

    public function Destroy(Request $request, $id)
    {
        $lang = $request -> input( 'lang' );

        $dateStatus = DateStatus::find( $id );
        // dd($dateStatus);

        $nextDateStatus = DateStatus::where('class', $dateStatus->class)->where('organization', \Session::get('organization'))->orderBy('sequence','asc')->where('sequence', '>', $dateStatus->sequence)->get();

        $nextDateStatus->each(function ($item) use($dateStatus){

            $item->update(['sequence'=> $dateStatus->sequence]);

            $dateStatus->sequence++;
        });

        $name = $dateStatus->status;
        
        if ( $dateStatus -> delete() ) {

            $message = ucwords( string_to_value( $lang, $name ) );

            if( $message == "" ) {

                $message = ucwords( string_to_value( 'en', $name ) );

            }
            
            return response() -> json( alert_delete( $message ) );

        }

    }

    public function show_data( $lang ) {

        $dateStatuses = DateStatus::selectRaw( "id, status, sequence, class, created_at, updated_at" ) -> get();

        $language_setting = new LanguageValidator;

        $json = $language_setting -> get_data( $lang  , [ 'id', 'status', 'sequence', 'class', 'created_at', 'updated_at' ], $dateStatuses );
        
        return $json;

    }

    public function getDateStatusName( Request $request, $id, $lang ) {

        $dateStatus = DateStatus::whereId( $id ) -> first();

        $message = ucwords( string_to_value( $lang, $dateStatus -> status) );

        return response() -> json( [ 'status' => $message ] );

    }

    public function getStatusName( $id, $lang ) {
        
        $dateStatuses = DateStatus::whereId( $id ) -> first();
        
        $statusName = ucwords( string_to_value( $lang, $dateStatuses -> status) );

        return $statusName;

    }
    
    public function byOrganization($collection) {
        
        $organization = \Session::get('organization');
        
        return $collection->where('organization', $organization);

    }
}
