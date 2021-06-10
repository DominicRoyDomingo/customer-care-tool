<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ContentItem;
use App\Models\PlatformItem;
use App\Models\ItemType;
use App\Models\ItemHistory;
use App\Models\Publishing;
use App\Models\PublishingHistory;
use App\Models\Brand;
use App\Models\Content;
use App\Models\Auth\User;
use App\Models\DateStatus;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index() {
        
        return view('backend.items.index');

    }

    public function store( Request $request ) 
    {

        $lang = $request->lang;
        
        $request->validate([
                "item_name"     => "required|unique:content,name->en,",
                "status"        => "required|numeric",
                "content"       => "required",
                "item_type"     => "required",
                "author_idea"   => "required",
                "notes"         => "nullable",
            ],
            [
                'item_name.required'    => 'Item Name is required.',
                'content.required'      => 'Content is required.',
                'author_idea.required'  => 'Author is required.',
                'status.required'       => 'Status is required.',
                'item_type.required'    => 'Item Type is required.',
            ]
        );

        $organization = \Session::get('organization');
        $itemName = ucwords($request->item_name);
        $item = ContentItem::create([
            "item_name"     => json_encode([
                            $lang => $itemName,
                            ]),
            "status"        => $request->status, 
            "content"       => $request->content, 
            "item_type"     => $request->item_type,
            "organization"  => $organization,
        ]);

        ItemHistory::create([
            "item"          => $item->id, 
            "author_task"   => $request->author_task, 
            "author_idea"   => $request->author_idea, 
            "status"        => $request->status,
            "notes"         => $request->notes
        ]);

        return response() -> json( alert_success( $request->item_name ) );

    }

    public function getList(Request $request) 
    {
        
        $contentId = $request->id;

        $lang = $request -> input( 'lang' );
        $content = $request -> input( 'content' );
        $search   = $request->input( 'search' );
        $filter = $request->input( 'filter' );
        $contentId = $request->input( 'id' );
        $filterValue = $request->input('filter_value');
        
        $language = 'English';

        if( $lang == 'it') { $language = 'Italian'; } if( $lang == 'de' ) { $language = 'German'; }

        $status = $this -> get_status( $lang );
        // dd($statuses);
        if($status == null) { return response() -> json( [ 'status' => $status]  ); }

        $contents = $this -> get_contents( $lang );

        $publishStatusId = $this -> getPublishStatusId();

        $getItems = ContentItem::orderBy( 'id', 'DESC' ) ->with([
            'item_history.author_idea_object' => function($query) {
                $query->select('id','first_name', 'last_name');
            },
            'date_status' => function($query) {
                $query->select('id','status', 'sequence');
            },
            'item_type_object' => function($query) {
                $query->select('id','type_name');
            },
            'content_name' => function($query) {
                $query->select('id','name');
            },
        ]);
        
        if($contentId != "") {
            $getItems->where('content', $contentId);
        }

        if($search != ''){

            $getItems = $getItems->where('item_name', 'LIKE', '%' . $search . '%')
                ->orWhere(function($subQuery) use ($search){   
               
                $subQuery->whereHas('item_type_object', function ( $query ) use ($search) {
                    $query->where('type_name',  'LIKE', '%' .$search . '%' );
                })
                ->orWhereHas('content_name', function ( $query ) use ($search) {
                    $query->where('name',  'LIKE', '%' . $search . '%' );
                })
                ->orWhereHas('date_status', function ( $query ) use ($search) {
                    $query->where('status',  'LIKE', '%' .  $search. '%' );
                })
                ->orWhereHas('item_history.author_idea_object', function ( $query ) use ($search) {
                    $query->whereRaw("concat(last_name, ' ', first_name) like '%$search%' ");
                });
            })->get();

        } else if($filterValue != '') {
            
            if($filter == 'name') $getItems = $getItems->where('item_name', 'LIKE', '%' . $filterValue . '%')->get();
            if($filter == 'status') $getItems = $getItems->where(function($subQuery) use ($filterValue){
                $subQuery->whereHas('date_status', function ( $query ) use ($filterValue) {
                    $query->where('status',  'LIKE', '%' .  $filterValue. '%' );
                });
            })->get();
            if($filter == 'content') $getItems = $getItems->where(function($subQuery) use ($filterValue){
                $subQuery->whereHas('content_name', function ( $query ) use ($filterValue) {
                    $query->where('name',  'LIKE', '%' .  $filterValue. '%' );
                });
            })->get();
            if($filter == 'item_type') $getItems = $getItems->where(function($subQuery) use ($filterValue){
                $subQuery->whereHas('item_type_object', function ( $query ) use ($filterValue) {
                    $query->where('type_name',  'LIKE', '%' .  $filterValue. '%' );
                });
            })->get();
            
        } else {

            $getItems = $getItems->get();

        }

        $organization = \Session::get('organization');

        $getItems = $getItems->where('organization', '=' , $organization);
        
       
        // if($filter == 'name')   $getItems = $getItems->sortBy('item_name');
        // if($filter == 'status') $getItems = $getItems->sortBy('date_status.status');
        // if($filter == 'content') $getItems = $getItems->sortBy('content_name.name');
        // if($filter == 'item_type') $getItems = $getItems->sortBy('item_type_object.type_name');

        $items = array();

        foreach( $getItems as $datas ) {

            $itemName = getTranslation($datas->item_name, $lang);

            $convertion = $this -> checkConvertion( $datas->id, $lang );
            
            $dateStatusName = getTranslation($datas->date_status->status, $lang);

            $itemTypeName = getTranslation($datas->item_type_object->type_name, $lang);

            $contentName = getTranslation($datas->content_name->name, $lang);

            array_push( $items, [
                
                'id' => $datas->id,

                'item_name' => $itemName,

                'status' => $datas->status,

                'content_name' => $contentName,

                'content_id' => $datas->content_name->id,

                'status_name' => $dateStatusName,

                'item_type' => $datas->item_type,

                'type_name' => $itemTypeName,

                'date_sequence' => $datas->date_status->sequence,

                'author_name' => $datas->item_history->author_idea_object->full_name,

                'author_id' => $datas->item_history->author_idea_object->id,

                'notes' => $datas->item_history->notes,

                'created_at' => date( 'F d, Y', strtotime( $datas->created_at ) ),

                'updated_at' => date( 'F d, Y', strtotime( $datas->updated_at ) ),

                'convertion' => ( ! empty( $convertion ) ? false : true ),

                'language' => $language

            ] );

        }
        // dd($items);
        return response() -> json( [ 'items' => $items, 'status' => $status, 'contents' => $contents, 'publishStatusId' => $publishStatusId]  );

    }

    public function getFilteredContent(Request $request) {

        $lang = $request -> input( 'lang' );
        
        $language = 'English';

        if( $lang == 'it') { $language = 'Italian'; } if( $lang == 'de' ) { $language = 'German'; }

    }

    public function getUsersAndItemTypes(Request $request) {

        $lang = $request -> input( 'lang' );

        $itemTypes = $this -> getItemTypes( $lang );

        $users = $this -> get_users( $lang );

        return response() -> json( [ 'item_types' => $itemTypes, 'users' => $users]  );

    }

    public function getContentUsersAndItemType(Request $request) {

        $lang = $request -> input( 'lang' );
        
        $itemTypes = $this -> getItemTypes( $lang );

        $users = $this -> get_users( $lang );

        $contents = $this -> get_contents( $lang );

        return response() -> json( [ 'item_types' => $itemTypes, 'users' => $users, 'contents' => $contents]  );

    }

    public function get_status( $lang ) {

        $organization = \Session::get('organization');

        $status = DateStatus::select( 'id' , 'status', 'sequence' )->where('class', 'item')->where('organization', $organization)->orderBy('sequence', 'ASC')->first();
       
        if($status == null) { return $status; }
        
        $status->status = getTranslation($status->status, $lang);
        
        return $status;
        
    }

    public function checkConvertion( $id, $lang ) {
        
        $item = ContentItem::whereId( $id ) -> first();
        
        $itemName = ucwords( string_to_value( $lang, $item -> item_name) );
       
        return $itemName;

    }

    public function getItemTypes( $lang ) {

        $itemTypes = ItemType::selectRaw( "id, type_name") -> get();

        $itemTypesArr = [];

        foreach( $itemTypes as $itemType ) {
            
            $itemTypeName = getTranslation($itemType->type_name, $lang);
            
            array_push( $itemTypesArr, [

                'item_type' => $itemType->id,

                'type_name' => $itemTypeName,

            ] );

        }

        return $itemTypesArr;

    }

    public function get_users( $lang ) {

        $organization = \Session::get('organization');

        $users = User::selectRaw( "id, first_name, last_name" )
            ->with('organizationUsers')
            ->whereHas('organizationUsers', function ($query) use($organization){
                return $query->where('organization', '=', $organization);
            }) 
            -> get();

        $users = $users->sortBy(function($user){
            return $user->full_name;
        });   
         
        return $users;
        
    }

    public function get_contents( $lang ) {

        $contents = Content::selectRaw( "id, name, status" ) -> get();

        $dateStatus = DateStatus::select( 'id' , 'status', 'sequence' )->where('class', 'content')->where('organization', \Session::get('organization'))->orderBy('sequence', 'DESC')->first();

        $contentsArr = [];

        foreach( $contents as $content ) {
            if($content->status == $dateStatus->id) {

                $contentName = getTranslation($content->name, $lang);
                
                array_push( $contentsArr, [

                    'id' => $content->id,

                    'content_name' => $contentName,

                ] );

            }

        }
         
        return $contentsArr;
        
    }

    public function getPublishStatusId() {

        $status = DateStatus::select( 'id' )->where('class', 'publishing')->orderBy('sequence', 'ASC')->first();

        return $status->id;

    }

    public function getFilteredOrSearched($items) {

    }


    // public function get_items( $lang ) {

    //     $items = Item::selectRaw( "id, item_name, status" ) -> get(); 

    //     $dateStatus = DateStatus::select( 'id' , 'status', 'sequence' )->where('class', 'item')->orderBy('sequence', 'DESC')->first();

    //     $itemsArr = array();
        
    //     foreach( $items as $item ) {
    //         if($item->status == $dateStatus->id) {
    //             $itemName = getTranslation($item->item_name, $lang);

    //             array_push( $itemsArr, [

    //                     'id'                => $item[ 'id' ],

    //                     'item_name'         => $itemName,         

    //                 ] 
    //             );
    //         }

    //     }
        
    //     return $itemsArr;
        
    // }
}
