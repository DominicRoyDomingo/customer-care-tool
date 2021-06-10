<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\LanguageValidator;

use App\Models\ContentItem;

use App\Models\DateStatus;

use App\Models\Content;

use App\Models\ItemType;

use App\Models\ItemHistory;

use App\Models\PlatformItem;

use App\Models\Publishing;

use App\Models\PublishingHistory;

use App\Models\Brand;

use App\Models\Auth\User;

use App\Models\PlatformType;

class ContentItemController extends Controller
{
    public function index(Request $request)
    {
        
        $lang = \App::getLocale();

        $content = Content::findOrFail($request->id);
        
        $translatedContentName = getTranslation($content->name, $lang);

        $dateStatus = DateStatus::select( 'id' , 'status', 'sequence' )->where('class', 'content')->where('organization', \Session::get('organization'))->orderBy('sequence', 'DESC')->first();

        $dateStatusName = getTranslation($dateStatus->status, $lang);
        // dd($dateStatus->id);
        if($dateStatus->id != $content->status)
        {
            abort(404);
        }

        return view('backend.content.items.index', ['contentId' => $request->id, 'contentName' => $translatedContentName]);

    }

    public function getList(Request $request) 
    {
        
        $contentId = $request->id;
        $search   = $request->input( 'search' );
        $filter = $request->input( 'filter' );
        $filterValue = $request->input('filter_value');

        $lang = $request -> input( 'lang' );
        
        $language = 'English';

        if( $lang == 'it') { $language = 'Italian'; } if( $lang == 'de' ) { $language = 'German'; }

        $dateStatus = $this -> getDateStatus( $lang );
        // dd($dateStatus);
        if($dateStatus == null) { return response() -> json( [ 'date_status' => $dateStatus]  ); }

        $publishStatusId = $this -> getPublishStatusId();

        $getItems = ContentItem::where('content', $contentId)->orderBy( 'id', 'DESC' ) ->with([
            'item_history.author_idea_object' => function($query) {
                $query->select('id','first_name', 'last_name');
            },
            'date_status' => function($query) {
                $query->select('id','status', 'sequence');
            },
            'item_type_object' => function($query) {
                $query->select('id','type_name');
            },
        ]);

        if($search != ''){

            $getItems = $getItems->where('item_name', 'LIKE', '%' . $search . '%')
                ->orWhere(function($subQuery) use ($search){   
               
                $subQuery->whereHas('item_type_object', function ( $query ) use ($search) {
                    $query->where('type_name',  'LIKE', '%' .$search . '%' );
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
            if($filter == 'item_type') $getItems = $getItems->where(function($subQuery) use ($filterValue){
                $subQuery->whereHas('item_type_object', function ( $query ) use ($filterValue) {
                    $query->where('type_name',  'LIKE', '%' .  $filterValue. '%' );
                });
            })->get();
            
        } else {

            $getItems = $getItems->get();

        }

        $organization = \Session::get('organization');

        $getItems = $getItems->where('organization', $organization);

        $items = array();

        foreach( $getItems as $datas ) {

            $itemName = getTranslation($datas->item_name, $lang);

            $convertion = $this -> checkConvertion( $datas->id, $lang );
            
            $dateStatusName = getTranslation($datas->date_status->status, $lang);

            $itemTypeName = getTranslation($datas->item_type_object->type_name, $lang);

            array_push( $items, [
                
                'id' => $datas->id,

                'item_name' => $itemName,

                'status' => $datas->status,

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
        
        return response() -> json( [ 'items' => $items, 'date_status' => $dateStatus, 'publishStatusId' => $publishStatusId]  );

    }


    public function showHistory(Request $request) 
    {
        return view('backend.content.items.history');

    }

    public function getHistory(Request $request) 
    {
        if($request->content == null || $request->id == null)
        {
            abort(404);
        }

        $lang = \App::getLocale();
        
        $firstItem = ItemHistory::where('item', $request->id)->first();

        $translatedItemName = getTranslation($firstItem->items->item_name, $lang);

        $translatedItemType = getTranslation($firstItem->item_history_type_name, $lang);

        $firstItemArr = [

            'item' => $translatedItemName,

            'item_type' => $translatedItemType,

        ];

        $items = ItemHistory::where('item', $request->id)->orderBy('updated_at', 'ASC')->get();

        $users = $this -> get_users( $lang );

        $itemsArr = array();

        foreach( $items as $item ) {
            
            $translatedStatusName = getTranslation($item->status_name, $lang);

            array_push( $itemsArr, [
                
                'id' => $item->id,

                'item_id' => $request->id,

                'date_started' => date( 'F d, Y', strtotime( $item->created_at ) ),

                'date_finished' => date( 'F d, Y', strtotime( $item->updated_at ) ),

                'author_task' => $item->author_task_name,

                'author_idea' => $item->author_idea_name,

                'status_name'    => $translatedStatusName,

                'date_sequence' => $item->item_date_sequence,

                'notes' => $item->notes,

                'author_id' => $item->author_id,

            ] );

        }

        $content = Content::findOrFail($request->content);

        $translatedContentName = getTranslation($content->name, $lang);

        $contentArr = [

            'id' => $content->id,

            'content_name' => $translatedContentName,

        ];

        return response() -> json( ['items' => $itemsArr, 'content' => $contentArr, 'users' => $users, 'firstItem' => $firstItemArr] );

    }

    public function store( Request $request ) 
    {

        $lang = $request->lang;
        
        $request->validate([
                "item_name"     => "required|unique:items,item_name->en,",
                "status"        => "required|numeric",
                "item_type"     => "required",
                "author_idea"   => "required",
                "notes"         => "nullable",
            ],
            [
                'item_name.required'    => 'Item Name is required.',
                'author_idea.required'  => 'Author is required.',
                'status.required'       => 'Status is required.',
                'item_type.required'    => 'Item Type is required.',
            ]
        );

        $itemName = ucwords($request->item_name);
        $organization = \Session::get('organization');
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

    public function update( Request $request ) 
    {

        $lang = ($request->language);
        
        $itemId = $request->id;
       
        $request->validate([
                "item_name" => "required|unique:content,name->en,".$itemId,
                "item_type" => "required",
                "author_idea"   => "required",
            ],
            [
                'item_name.required' => 'Item Name is required.',
                'item_name.unique' => $request->item_name. ' is already taken.',
                'item_type.required' => 'Item Type is required.',
                'author_idea.required'  => 'Author is required.',
            ]
        );
        
        $item = ContentItem::findOrFail($itemId);
        
        $itemVal = string_add_json( $lang, ucwords( $request -> item_name ), string_remove( $lang, $item -> item_name ) );
        
        $item->update([
            "item_name"     => $itemVal,
            "item_type"     => $request->item_type
        ]);

        $itemHistory = ItemHistory::where('item', $item->id)->first();
        $itemHistory->update([
            "author_idea"   => $request->author_idea,
            "author_task"   => $request->author_task,
        ]);
        return response() -> json( alert_update( $request->item_name ) );

    }

    public function createPublishing( Request $request ) 
    {

        $lang = $request->lang;
        
        $request->validate([
                "item_publishing_name"      => "required",
                "item_publishing_author"    => "required",
                "item_publishing_platform"  => "required",
                "notes"                     => "nullable",
            ],
            [
                'item_publishing_name.required'         => 'Publishing Name is required.',
                'item_publishing_author.required'       => 'Author is required.',
                'item_publishing_platform.required'     => 'Platform is required.',
            ]
        );

        $publishingName = ucwords($request->item_publishing_name);
        $publishing = Publishing::create([
            "name"          => json_encode([
                            $lang => $publishingName,
                            ]),
            "item_id"     => $request->item_id
        ]);

        PublishingHistory::create([
            "publishing_id" => $publishing->id, 
            "publisher"     => $request->item_publishing_publisher, 
            "author"        => $request->item_publishing_author, 
            "platform"      => $request->item_publishing_platform,
            "status"        => $request->item_publishing_status,
            "notes"         => $request->item_publishing_notes
        ]);

        return response() -> json( alert_success( $publishingName ) );

    }

    public function updateStatus( Request $request ) {

        $lang = $request->lang;

        $request->validate([
                "status" => "required",
                "author_idea" => "required",
            ],
            [
                'status.required' => 'Status is required.',
                'author_idea.required' => 'Person in-charge is required.',
            ]
        );
        
        // $en = ucwords($request->content);
        $item = ContentItem::findOrFail($request->id);
        
        $itemName = getTranslation($item->item_name, $lang);

        $item->update([
            "status"    => $request->status,
        ]);

        $statusName = getTranslation($item->date_status->status, $lang);
        
        ItemHistory::updateOrCreate(
            [
                'status' => $request->status
            ],
            [
                "item"          => $item->id,
                "author_task"   => $request->author_task,
                "author_idea"   => $request->author_idea,
                "notes"         => $request->notes,
                "status"        => $request->status,
            ]
        );

        return response() -> json( alert_status( $itemName , $statusName) );

    }

    public function Destroy( Request $request, $id )
    {
        $lang = $request -> input( 'lang' );
         
        $item = ContentItem::find( $id );

        $name = $item->item_name;

        $message = ucwords( string_to_value( $lang, $name ) );

        if(!$item->publishings()->exists())
        {
            if ( $item -> delete() ) {
    
                return response() -> json( alert_delete( $message ) );
    
            }
        }
    
        return response() -> json( ['name' => $message] );
        
    }

    public function getLastStatus()
    {

        $lang = \App::getLocale();

        $dateStatus = $this->checkDateStatusItemForExec();
        
        // $dateStatus = DateStatus::select( 'id' , 'status', 'sequence' )->where('class', 'item')->orderBy('sequence', 'DESC')->first();

        $dateStatusName = getTranslation($dateStatus->status, $lang);

        $platforms = $this -> getPlatforms( $lang );

        $users = $this -> get_users( $lang );

        return response() -> json( [ 'lastStatus' => $dateStatusName, 'users' => $users, 'platforms' => $platforms ] );
    }

    public function getBrandsAndPlatformTypes()
    {

        $lang = \App::getLocale();
        
        $brands = $this -> getBrands( $lang );

        $platformTypes = $this -> getPlatformTypes( $lang );

        return response() -> json( [ 'brands' => $brands, 'platformTypes' => $platformTypes ] );
    }

    public function getDateStatus( $lang ) {

        $organization = \Session::get('organization');

        $status = DateStatus::select( 'id' , 'status', 'sequence', 'organization' )->where('class', 'item')->where('organization', $organization)->orderBy('sequence', 'ASC')->first();

        if($status == null) { return $status; }

        $status->status = getTranslation($status->status, $lang);

        return $status;

    }

    public function getPublishStatusId() {

        $organization = \Session::get('organization');

        $status = DateStatus::select( 'id' ,'organization' )->where('class', 'publishing')->where('organization', $organization)->orderBy('sequence', 'ASC')->first();

        return $status->id;

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

    public function getPlatforms( $lang ) {

        $platforms = PlatformItem::selectRaw( "id, brand, platform_type") -> get();

        $platformsArr = [];

        foreach( $platforms as $platform ) {

            $platformName = $platform->platform_item_name;
            
            array_push( $platformsArr, [

                'id' => $platform->id,

                'platform_name' => $platformName,

            ] );

        }
     
        return $platformsArr;

    }

    public function getBrands( $lang ) {

        $brands = Brand::selectRaw( "id, name" ) -> get();

        // $language_setting = new LanguageValidator;

        // $json = $language_setting -> get_data( $lang  , [ 'id', 'name' ], $brands );
        
        return $brands;
        
    }

    public function checkConvertion( $id, $lang ) {
        
        $item = ContentItem::whereId( $id ) -> first();
        
        $itemName = ucwords( string_to_value( $lang, $item -> item_name) );
       
        return $itemName;

    }

    public function getTranslatedItemName( $id, $lang ) {
        
        $item = ContentItem::whereId( $id ) -> first();

        $message = ucwords( string_to_value( $lang, $item -> item_name) );

        return response() -> json( $message );

    }

    public function getTranslation($name, $lang) {

        $availableLanguage = array_key_first(json_decode($name, true));

        return isset(json_decode($name)->$lang) ? json_decode($name)->$lang : json_decode($name)->$availableLanguage;

    }

    public function getNextStatus(Request $request, $id) {

        $lang = $request->lang;

        $users = $this -> get_users( $lang );
        
        $status = DateStatus::select( 'id' , 'status', 'sequence', 'organization' )->where('class', 'item')->where('organization', \Session::get('organization'))->orderBy('sequence', 'ASC')->where('sequence', '>', $id)->first();
       
        if(is_null($status)){

            $status = DateStatus::select( 'id' , 'status', 'sequence', 'organization' )->where('class', 'item')->where('organization', \Session::get('organization'))->orderBy('sequence', 'ASC')->where('sequence', $id)->first();

            $status->status = getTranslation($status->status, $lang);;
            
            return response() -> json([ 'status' => $status, 'users' => $users]);
        }
        
        $status->status = getTranslation($status->status, $lang);
        // $json = $language_setting -> get_data( $lang  , [ 'id', 'status', 'sequence' ], $status );
        // dd($status);
        return response() -> json([ 'status' => $status, 'users' => $users ]);

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

    public function getPlatformTypes( $lang ) {

        $platform_type = PlatformType::select('id')->orderBy( 'id', 'DESC' ) -> get();
        // dd($platform_type->toArray());
        $show_platform = array();

        foreach( $platform_type as $datas ) {

            $name = $this -> getNamePlatformType( $datas[ 'id' ], $lang );
            
            $base_name = ( ! empty( $name ) ? $name : $this -> getNamePlatformType( $datas[ 'id' ], 'en' ) );

            if( $name == '' && $base_name == '' ) {

                $base_name_italy = $this -> getNamePlatformType( $datas[ 'id' ], 'it' );

                if( $base_name_italy !== '' ) {

                    $base_name = $base_name_italy;

                } else {

                    $base_name = $this -> getNamePlatformType( $datas[ 'id' ], 'de' );

                }
                
            }
            
            array_push( $show_platform, [

                'id' => $datas[ 'id' ],

                'name' => $base_name,

            ] );

        }
        
        return $show_platform;

    }

    public function getNamePlatformType( $id, $lang ) {
        
        $platform_type = PlatformType::select('name')->whereId( $id ) -> first();

        $name = getTranslation($platform_type->name, $lang);

        return $name;

    }

    

    public function getUsersAndItemTypes(Request $request) {

        $lang = $request -> input( 'lang' );

        $itemTypes = $this -> getItemTypes( $lang );

        $users = $this -> get_users( $lang );

        return response() -> json( [ 'item_types' => $itemTypes, 'users' => $users ]  );

    }

    public function checkDateStatusItemForExec() {
        
        return DateStatus::select( 'id' , 'status', 'sequence', 'organization' )->where('class', 'item')->where('organization', \Session::get('organization'))->orderBy('sequence', 'DESC')->first();

    }

}
