<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Content;

use App\Models\Brand;

use App\Models\DateStatus;

use App\Models\ContentHistory;

use App\Models\LanguageValidator;

use App\Models\ItemType;

use App\Models\Auth\User;

use Illuminate\Contracts\Filesystem\Filesystem;

use Illuminate\Support\Collection;

class ContentController extends Controller
{
    

    public function index() {

        return view('backend.content.index');

    }


    public function getList( Request $request ) {
        
        $lang   = $request->input( 'lang' );
        $page   = $request->input( 'page' );
        $search = $request->input('search');
        $filter = $request->input( 'filter' );
        $filterValue = $request->input('filter_value');

        $statuses = $this -> get_status( $lang );
        // dd($statuses);
        if($statuses == null) { return response() -> json( [ 'statuses' => $statuses]  ); }
        
        $content = $this -> show_data( $lang, $page, $search, $filter, $filterValue);

        return response() -> json( [ 'contents' => $content['contentArr'], 'statuses' => $statuses,  'totalRecords' => $content['totalRows'],'page' => $page, 'search' => $search]  );

    }


    public function postContent( Request $request ) {

        $lang = $request -> input( 'lang' );

        $request->validate([
                "content"   => "required|unique:content,name->en",
                "status" => "required",
                "author_idea" => "required",
                "author_task" => "required",
                "notes" => "nullable",
            ],
            [
                'content.required' => 'Topic is required.',
                'content.unique' =>  $request->content.' is already taken.',
                'status.required' => 'Status is required.',
                'author_idea.required' => 'Author is required.',
                'author_task.required' => 'Author is required.',
            ]
        );
        
        $contentName = ucwords($request->content);

        $organization = \Session::get('organization');
        
        $content = Content::create([
            "name"          => json_encode([
                            $lang => $contentName,
                            ]), 
            "status"        => $request->status, 
            "clipart"       => 'cliparts/megaphone.png',
            "organization"  => $organization
        ]);

        ContentHistory::create([
            "content"       => $content->id, 
            "author_task"   => $request->author_task, 
            "author_idea"   => $request->author_idea, 
            "status"        => $request->status,
            "notes"         => $request->notes
        ]);
        return response() -> json( alert_success( $contentName ) );

    }

    public function showHistory(Request $request) 
    {
        return view('backend.content.history');

    }

    public function getHistory(Request $request) 
    {
        if($request->id == null)
        {
            abort(404);
        }

        $lang = \App::getLocale();
        
        $firstContent = ContentHistory::where('content', $request->id)->with([
            'contents' => function($query) {
                $query->select('id','name');
            },
            'contents.items' => function($query) {
                $query->select('id','item_name', 'content');
            },
        ])-> first();
        // dd($firstContent->toArray());
        $translatedContentName = getTranslation($firstContent->contents->name, $lang);
        $itemNames = collect($firstContent->contents->items)->map(function ($item) {

            // $tag['id']   = $tag['id'];
            $item['item_name'] = getTranslation($item['item_name'], locale());
        
            return $item;
        
        });
        // dd($sad->toArray());
        $firstContentArr = [

            'content_name'  => $translatedContentName,

            'items'         => $itemNames->toArray(),

        ];

        // dd($firstContentArr);

        $contents = ContentHistory::where('content', $request->id)->orderBy('updated_at', 'ASC')->with([
            'date_status' => function($query) {
                $query->select('id', 'status', 'sequence');
            },
            'author_task_object' => function($query) {
                $query->select('id', 'first_name', 'last_name');
            },
            'author_idea_object' => function($query) {
                $query->select('id', 'first_name', 'last_name');
            },
        ])-> get();
        // dd($contents->toArray());
        $users = $this -> get_users( $lang );

        $itemTypes = $this -> getItemTypes( $lang );

        // $dateStatus = $this -> getDateStatus( $lang );

        $contentsArr = array();

        foreach( $contents as $content ) {
            
            $translatedStatusName = getTranslation($content->date_status->status, $lang);
            
            array_push( $contentsArr, [
                
                'id' => $content->id,

                'content_id' => $request->id,

                'date_started' => date( 'F d, Y', strtotime( $content->created_at ) ),

                'date_finished' => date( 'F d, Y', strtotime( $content->updated_at ) ),

                'author_task' => $content->author_task_object->full_name,

                'author_idea' => $content->author_idea_object->full_name,

                'status_name'    => $translatedStatusName,

                'date_sequence' => $content->date_status->sequence,

                'notes' => $content->notes,

                'author_id' => $content->author_idea_object->full_name,

            ] );

        }


        return response() -> json( ['contents' => $contentsArr, 'firstContent' => $firstContentArr] );

    }


    public function updateContent( Request $request ) {

        $lang = ($request->language);
        
        $contentId = $request->id;
       
        $request->validate([
                "content" => "required|unique:content,name->en,".$contentId,
                // "clipart" => "required",
                "author_idea" => "required",
                "author_task" => "required",
            ],
            [
                'content.required' => 'Topic is required.',
                'content.unique' => $request->content. ' is already taken.',
                'author_idea.required' => 'Author is required.',
                'author_task.required' => 'Author is required.',
                // 'clipart.required' => 'Clipart is required.',
            ]
        );
        // dd($request->author_idea);
        $content = Content::findOrFail($contentId);
        
        $contentVal = string_add_json( $lang, ucwords( $request -> content ), string_remove( $lang, $content -> name ) );
        // dd($contentVal);
        $content->update([
            "name"      => $contentVal, 
            "clipart"   => 'cliparts/megaphone.png'
        ]);
        
        $contentHistory = ContentHistory::where('content', $content->id)->latest('updated_at')->first();;
        $contentHistory->update([
            "author_idea"   => $request->author_idea,
            "author_task"   => $request->author_task
        ]);
        return response() -> json( alert_update( $request->content ) );

    }

    public function updateStatus( Request $request ) {

        $lang = $request->lang;

        $request->validate([
                "status" => "required",
                "author_idea" => "required",
                "notes" => "nullable",
            ],
            [
                'status.required' => 'Status is required.',
                'author_idea.required' => 'Person in-charge is required.',
            ]
        );
        // dd($request->status);
        $content = Content::findOrFail($request->id);

        $contentName = getTranslation($content->name, $lang);

        $content->update([
            "status"    => $request->status,
        ]);
        
        $statusName = getTranslation($content->date_status_name, $lang);

        ContentHistory::updateOrCreate(
            [
                'status' => $request->status
            ],
            [
                "content"       => $content->id,
                "author_task"   => $request->author_task,
                "author_idea"   => $request->author_idea,
                "notes"         => $request->notes,
                "status"        => $request->status,
            ]
        );

        return response() -> json( alert_status( $contentName , $statusName) );

    }

    public function deleteContent( Request $request, $id ) {

        $lang = $request -> input( 'lang' );
         
        $content = Content::find( $id );

        $name = $content->name;

        $message = ucwords( string_to_value( $lang, $name ) );

        if(!$content->items()->exists())
        {
            if ( $content -> delete() ) {
    
                return response() -> json( alert_delete( $message ) );
    
            }
        }
    
        return response() -> json( ['name' => $message] );    

    }

    public function getItemStatus(Request $request) {

        $lang = $request -> input( 'lang' );

        $dateStatus = $this -> getDateStatus( $lang );

        $users = $this -> get_users( $lang );

        $itemTypes = $this -> getItemTypes( $lang );
        
        $dateStatus = DateStatus::select( 'id' , 'status', 'sequence' )->where('class', 'content')->where('organization', \Session::get('organization'))->orderBy('sequence', 'DESC')->first();

        $dateStatusItem = DateStatus::select( 'id' , 'status', 'sequence' )->where('class', 'item')->where('organization', \Session::get('organization'))->orderBy('sequence', 'ASC')->first();

        $dateStatusName = getTranslation($dateStatus->status, $lang);

        return response() -> json( [ 'date_status' => $dateStatus, 'users' => $users, 'item_types' => $itemTypes, 'lastStatus' => $dateStatusName, 'date_status_item' => $dateStatusItem] );

    }

    public function getLastStatus()
    {

        $lang = \App::getLocale();
        
        $dateStatus = DateStatus::select( 'id' , 'status', 'sequence' )->where('class', 'content')->where('organization', \Session::get('organization'))->orderBy('sequence', 'DESC')->first();
        
        $dateStatusName = getTranslation($dateStatus->status, $lang);

        return response() -> json( [ 'lastStatus' => $dateStatusName ] );
    }

    
    public function getContentName( Request $request, $id, $lang ) {

        $content = Content::whereId( $id ) -> first();

        $message = ucwords( string_to_value( $lang, $content -> name) );

        return response() -> json( [ 'name' => $message ] );

    }

    public function getStatusName( $id, $lang ) {

        $dateStatus = DateStatus::whereId( $id ) -> first();
        
        $statusName = getTranslation($dateStatus->status, $lang);
        
        return $statusName;

    }

    public function getNextStatus(Request $request, $id) {

        $lang = $request->lang;

        $users = $this -> get_users( $lang );
        
        $status = DateStatus::select( 'id' , 'status', 'sequence', 'organization' )->where('class', 'content')->where('organization', \Auth::user()->organizationUsers()->first()->organization)->orderBy('sequence', 'ASC')->where('sequence', '>', $id)->first();
        // dd($status);
        if(is_null($status)){
           
            $status = DateStatus::select( 'id' , 'status', 'sequence', 'organization' )->where('class', 'content')->where('organization', \Auth::user()->organizationUsers()->first()->organization)->orderBy('sequence', 'ASC')->where('sequence', $id)->first();

            $status->status = getTranslation($status->status, $lang);;
            
            return response() -> json([ 'status' => $status, 'users' => $users ]);
        }
        
        $status->status = getTranslation($status->status, $lang);
        // $json = $language_setting -> get_data( $lang  , [ 'id', 'status', 'sequence' ], $status );

        return response() -> json([ 'status' => $status, 'users' => $users ]);

    }

    public function getDateStatus( $lang ) {

        $status = DateStatus::select( 'id' , 'status', 'sequence' )->where('class', 'item')->where('organization', \Session::get('organization'))->orderBy('sequence', 'ASC')->first();

        if($status == null) { return $status; }

        $status->status = getTranslation($status->status, $lang);

        return $status;

    }


    public function view_content( $data, $lang ) {

        return [

        'id' => $data -> id,

        'name' => string_to_value( $lang, $data -> name ),

        'created_at' => $data -> created_at,

        'updated_at' => $data -> updated_at,

        ];

    }

    
    public function show_content( $lang , $id) {

        $contents = Content::select('id','name', 'status', 'clipart')->where('id', $id)->get();

        $language_setting = new LanguageValidator;

        $json = $language_setting -> get_data( $lang  , [ 'id', 'name', 'status', 'clipart', 'full_name', 'date_sequence'], $contents );
       
        return $json;

    }

    public function show_data( $lang, $page, $search, $filter, $filterValue ) {

        $contents = Content::selectRaw( "id, name, status, organization" ) ->with([
            'date_status',
            'content_history.author_task_object' => function($query) {
                $query->select('id', 'first_name', 'last_name');
            },
            'content_history.author_idea_object' => function($query) {
                $query->select('id', 'first_name', 'last_name');
            },
        ])->withCount('items');
            // dd($contents);
        if($search != null){

            $contents = $contents->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere(function($subQuery) use ($search){   
                $subQuery->whereHas('date_status', function ( $query ) use ($search) {
                    $query->where('status',  'LIKE', '%' .$search . '%' );
                })
                ->orWhereHas('content_history.author_task_object', function ( $query ) use ($search) {
                    $query->whereRaw("concat(last_name, ' ', first_name) like '%$search%' ");
                })
                ->orWhereHas('content_history.author_idea_object', function ( $query ) use ($search) {
                    $query->whereRaw("concat(last_name, ' ', first_name) like '%$search%' ");
                });
            })->get();

        } else if($filterValue != '') {
            
            if($filter == 'name') $contents = $contents->where('name', 'LIKE', '%' . $filterValue . '%')->get();
            if($filter == 'status') $contents = $contents->where(function($subQuery) use ($filterValue){
                $subQuery->whereHas('date_status', function ( $query ) use ($filterValue) {
                    $query->where('status',  'LIKE', '%' .  $filterValue. '%' );
                });
            })->get();
            if($filter == 'author') $contents = $contents->where(function($subQuery) use ($filterValue){   
                $subQuery->whereHas('content_history.author_task_object', function ( $query ) use ($filterValue) {
                    $query->whereRaw("concat(last_name, ' ', first_name) like '%$filterValue%' ");
                })->orWhereHas('content_history.author_idea_object', function ( $query ) use ($filterValue) {
                    $query->whereRaw("concat(last_name, ' ', first_name) like '%$filterValue%' ");
                });
            })->get();
            
        } else{
            $contents = $contents->get();
        }

        $organization = \Session::get('organization');

        $contents = $contents->where('organization', $organization);

        $contentCount = $contents->count();
        
        $contents = $contents->forPage($page,10);
       
        $content = array();
        
        foreach( $contents as $datas ) {
          
            if( $datas[ 'name' ] != null ) {
                
                $statusName = getTranslation( $datas->date_status->status , $lang );
                
                $contentName = getTranslation($datas->name, $lang);
                
                array_push( $content, [

                    'id' => $datas[ 'id' ],

                    'name' => $contentName,

                    'status_name' => $statusName,

                    'date'   => date( 'F d, Y', strtotime( $datas->content_history->updated_at ) ),

                    'status' => $datas->status,

                    'author_name' => $datas->content_history->author_idea_object->full_name,

                    'author_id' => $datas->content_history->author_idea_object->id,

                    'person_in_charge_name' => $datas->content_history->author_task_object->full_name,

                    'person_in_charge_id' => $datas->content_history->author_task_object->id,

                    'notes' => $datas->content_history->notes,

                    'date_sequence' => $datas->date_status->sequence, 

                    'items' => $datas->items_count,

                ] );

            }

        }

        $datas = [
            'totalRows' => $contentCount,
            'contentArr' => $content
        ];
        
        return $datas;

    }


    public function get_status( $lang ) {

        $organization = \Session::get('organization');

        $status = DateStatus::select( 'id' , 'status', 'sequence', 'organization' )->where('class', 'content')->where('organization', $organization)->orderBy('sequence', 'ASC')->first();

        if($status == null) { return $status; }
    
        $status->status = getTranslation($status->status, $lang);

        return $status;
        
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

    public function fetchUsers( Request $request ) {

        $lang   = $request->input( 'lang' );
        
        $users = $this -> get_users( $lang );

        return response() -> json( [ 'users' => $users]  );
    }

    public function get_contents_by_brand( $lang , $id ) {

        $contents = Content::select('id','name', 'status', 'brand', 'clipart')->where('brand', $id)->get();

        $language_setting = new LanguageValidator;

        $json = $language_setting -> get_data( $lang  , [ 'id', 'name', 'status', 'brand', 'clipart' ], $contents );

        return $json;
        
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

}