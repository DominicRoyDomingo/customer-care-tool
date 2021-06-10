<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\DateStatus;

use App\Models\Publishing;

use App\Models\PublishingHistory;

use App\Models\PublishingTag;

use App\Models\Tags;

use App\Models\Item;

use App\Models\PlatformItem;

use App\Models\PlatformType;

use App\Models\PublishingRelation;

use App\Models\Brand;

use App\Models\LanguageValidator;

use App\Models\Auth\User;

use DB;

class PublishingController extends Controller
{
    public function index() {
        
        return view('backend.publishing.index');

    }

    public function showHistory(Request $request) 
    {
        return view('backend.publishing.history');

    }

    public function postPublishing( Request $request ) {

        $lang = $request -> input( 'lang' );

        $request->validate([
                "name"   => "required|unique:publishing,name->en",
                "item" => "required",
                "author" => "required",
                "platform" => "required",
                "notes" => "nullable",
            ],
            [
                'name.required'     => 'Publishing Name is required.',
                'name.unique'       =>  $request->name.' is already taken.',
                'author.required'   => 'Author is required.',
                'platform.required' => 'Platform is required.',
                'item.required'     => 'Item is required.',
            ]
        );
        
        $publishingName = ucwords($request->name);

        $publishing = Publishing::create([
            "name"      => json_encode([
                        $lang => $publishingName,
                        ]),
            "item_id"   => $request->item
        ]);

        PublishingHistory::create([
            "publishing_id" => $publishing->id, 
            "publisher"     => $request->publisher, 
            "author"        => $request->author, 
            "status"        => $request->status,
            "platform"      => $request->platform,
            "notes"         => $request->notes
        ]);

        return response() -> json( alert_success( $publishingName ) );

    }

    public function attachTags( Request $request ) {

        $lang = $request -> input( 'lang' );

        $request->validate([
                "tag" => "required",
            ],
            [
                'tag.required' => 'Tags is required.',
            ]
        );

        foreach(json_decode($request->tag) as $tag) {
            PublishingTag::updateOrCreate(
                [
                    "publishing_id" => $request->publishing_id, 
                    "tag" => $tag
                ], 
                [
                    "publishing_id" => $request->publishing_id, 
                    "tag" => $tag,
                ]
            );
    
            $publishingTags = PublishingTag::where('tag', '=', $tag)->where('publishing_id', '!=', $request->publishing_id)->get();

            if ($publishingTags != null) {
                foreach ($publishingTags as $publishingTag) {
                    PublishingRelation::updateOrCreate(
                    [
                        'publishing' => $request->publishing_id,
                        'linked_publishing' => $publishingTag->publishing_id,
                        'tag' => $tag,
                    ], 
                    [
                        'publishing' => $request->publishing_id,
                        'linked_publishing' => $publishingTag->publishing_id,
                        'tag' => $tag,
                        'status' => 'To be Linked',
                    ]);

                    PublishingRelation::updateOrCreate(
                    [
                        'publishing' => $publishingTag->publishing_id,
                        'linked_publishing' => $request->publishing_id,
                        'tag' => $tag,
                    ], 
                    [
                        'publishing' => $publishingTag->publishing_id,
                        'linked_publishing' => $request->publishing_id,
                        'tag' => $tag,
                        'status' => 'To be Linked',
                    ]);
                }
            }
        }

        return response() -> json( alert_success( 'test' ) );

    }

    public function removeTags( Request $request ) {

        $lang = $request -> input( 'lang' );

        $request->validate([
                "tag" => "required",
            ],
            [
                'tag.required' => 'Tags is required.',
            ]
        );
        $publishingTag = PublishingTag::where('publishing_id', $request->publishing_id)->where('tag', $request->tag);

        $publishingTagName = $publishingTag->first()->tags_name;
        
        $publishingRelations = PublishingRelation::where('publishing', '=', $publishingTag->first()->publishing_id)->where('tag', '=', $request->tag)->get();
      
        foreach ($publishingRelations as $publishingRelation ) {
            $publishingId = $publishingRelation->publishing;
            $linkedPublishingId = $publishingRelation->linked_publishing;
            PublishingRelation::where('publishing', '=', $publishingId)->where('tag', '=', $request->tag)->delete();
            PublishingRelation::where('publishing' , '=' , $linkedPublishingId)->where('linked_publishing', '=', $publishingId)->where('tag', '=', $request->tag)->delete();
        }

        $publishingTag->delete();
        
        return response() -> json( alert_success( $publishingTagName ) );

    }

    public function createLink( Request $request ) {

        $lang = $request -> input( 'lang' );

        $request->validate([
                "linked_publishing" => "required",
                "publishing"        => "required",
                "status"            => "required",
            ],
            [
                'linked_publishing.required'    => 'Linked publishing is required.',
                'publishing.required'           => 'Publishing is required.',
                'status.required'                => 'Status is required'
            ]
        );
        
        if($request->status == 'Link Created') {

            PublishingRelation::where('publishing', '=', $request->publishing)->where('linked_publishing', '=' ,$request->linked_publishing)->update(['status' => 'To Be Linked']);

            return;
        }

        if($request->status == 'To Be Linked') {

            PublishingRelation::where('publishing', '=', $request->publishing)->where('linked_publishing', '=' ,$request->linked_publishing)->update(['status' => 'Link Created']);

            return;

        }

        return;

    }

    public function updatePublishing( Request $request ) {

        $lang = ($request->language);
        
        $publishingId = $request->id;
       
        $request->validate([
                "name"      => "required|unique:publishing,name->en,".$publishingId,
                "item"      => "required",
                "author"    => "required",
                "platform"  => "required",
            ],
            [
                'name.required'     => 'Publishing Name is required.',
                'name.unique'       => $request->name. ' is already taken.',
                'item.required'     => 'Item is required.',
                'author.required'   => 'Author is required.',
                'platform.required' => 'Platform is required.',
            ]
        );
        
        $publishing = Publishing::findOrFail($publishingId);
        
        $publishingVal = string_add_json( $lang, ucwords( $request -> name ), string_remove( $lang, $publishing -> name ) );
        
        $publishing->update([
            "name"      => $publishingVal, 
            "item_id"   => $request->item
        ]);

        $publishingHistory = PublishingHistory::where('publishing_id', $publishing->id)->first();
        $publishingHistory->update([
            "author"      => $request->author,
            "publisher"   => $request->publisher,
            "platform"    => $request->platform,
        ]);
        
        return response() -> json( alert_update( $request->name ) );

    }

    public function updateStatus( Request $request ) {

        $lang = $request->lang;

        $request->validate([
                "status" => "required",
                "author" => "required",
                "notes" => "nullable",
            ],
            [
                'status.required' => 'Status is required.',
                'author.required' => 'Person in-charge is required.',
            ]
        );

        $publishing = Publishing::findOrFail( $request->id );
 
        $publishingName = getTranslation( $publishing->name, $lang );

        $publishingHistory = PublishingHistory::updateOrCreate(
            [
                'publishing_id' => $publishing->id,
                'status'        => $request->status
            ],
            [
                "publishing_id" => $publishing->id,
                "author"        => $request->author,
                "publisher"     => $request->publisher,
                "notes"         => $request->notes,
                "platform"      => $request->platform,
                "status"        => $request->status,
            ]
        );

        $statusName = getTranslation( $publishingHistory->date_status_name, $lang );

        return response() -> json( alert_status( $publishingName , $statusName) );

    }

    public function deletePublishing( Request $request, $id ) {

        $lang = $request -> input( 'lang' );
         
        $publishing = Publishing::find( $id );

        $name = $publishing->name;

        if ( $publishing -> delete() ) {

            $message = ucwords( string_to_value( $lang, $name ) );

            return response() -> json( alert_delete( $message ) );

        }

    }

    public function getHistory(Request $request) 
    {
        if($request->id == null)
        {
            abort(404);
        }

        $lang = \App::getLocale();

        $publishings = PublishingHistory::where('publishing_id', $request->id)->with([
            'dateStatus' => function($query) {
                $query->select('id','status','sequence');
            },
            'publisher_object' => function($query) {
                $query->select('id','first_name','last_name');
            },
            'author_object' => function($query) {
                $query->select('id','first_name','last_name');
            },
            'publishing' => function($query) {
                $query->select('id','name');
            },
            'publishing.publishing_tag' => function($query) {
                $query->select('id','publishing_id','tag');
            },
            'publishing.publishing_relation' => function($query) {
                $query->select('id','linked_publishing','publishing', 'status');
            },
        ])->orderBy('updated_at', 'ASC')->get();

      
        $publishingsArr = array();

        foreach( $publishings as $publishing ) {
            
            $translatedStatusName = getTranslation($publishing->dateStatus->status, $lang);

            $translatedPublishingName = getTranslation($publishing->publishing->name, $lang);

            $publishingTags = $publishing->publishing->publishing_tag;
            
            array_push( $publishingsArr, [
                
                'id'                    => $publishing->id,

                'publishing_id'         => $request->id,

                'publishing_name'       => $translatedPublishingName,

                'date_started'          => date( 'F d, Y', strtotime( $publishing->created_at ) ),

                'date_finished'         => date( 'F d, Y', strtotime( $publishing->updated_at ) ),

                'publisher'             => $publishing->publisher_object->full_name,

                'author'                => $publishing->author_object->full_name,

                'status_name'           => $translatedStatusName,

                'date_sequence'         => $publishing->dateStatus->sequence,

                'notes'                 => $publishing->notes,

                'platform_id'           => $publishing->platform,

                'author_id'             => $publishing->author,

                'tags'                  => $publishing->publishing->publishing_tags != null ? $publishing->publishing->publishing_tags : '',

                'tags_name'             => $publishingTags != null ? $publishingTags : '',

                'publishing_relations'  => $this->publishingRelation($publishing->publishing->publishing_relation)->unique(function ($item) {
                    return $item['publishing'].$item['linked_publishing'];
                }),

            ] );

        }

        return response() -> json( ['publishings' => $publishingsArr] );

    }

    public function getTags() 
    {

        $lang = \App::getLocale();

        $tags = $this -> get_tags ( $lang );

        return response() -> json( ['tags' => $tags] );

    }

    public function getBrandsAndPlatformTypes() {

        $lang = \App::getLocale();

        $platformTypes = $this -> getPlatformTypes( $lang );

        $brands = $this -> getBrands( $lang );

        return response() -> json( ['platformTypes' => $platformTypes , 'brands' => $brands] );
    }

    public function getOtherTags() 
    {

        $otherTags = PublishingTag::groupBy('publishing_id')->with([
            'publishing' => function($query) {
                $query->select('id','name');
            },
        ])->get();
       
        $otherTagsArr = array();

        foreach ($otherTags as $otherTag ) {

            $tagsNameArr = array() ;

            foreach ($otherTag->publishing->publishing_tag as $tag ) {

                array_push($tagsNameArr, $tag->tags_name);

            }

            array_push( $otherTagsArr, [

                'id'                => $otherTag->id,

                'publishing_id'     => $otherTag->publishing_id,

                'tag'               => $otherTag->publishing->publishing_tags,

                'publishing_name'   => getTranslation($otherTag->publishing->name, locale()),

                'tags_name'         => $tagsNameArr

            ] );

        }

        return response() -> json( ['otherTags' => $otherTagsArr] );

    }

    public function getUsers()
    {
        $lang = \App::getLocale();

        $users = $this -> get_users( $lang );

        return response() -> json( ['users' => $users] );

    }

    public function getNextStatus(Request $request, $id) {

        $lang = $request->lang;

        $users = $this -> get_users( $lang );

        $status = DateStatus::select( 'id' , 'status', 'sequence' )->where('class', 'publishing')->where('organization', \Session::get('organization'))->orderBy('sequence', 'ASC')->where('sequence', '>', $id)->first();
       
        if(is_null($status)){

            $status = DateStatus::select( 'id' , 'status', 'sequence' )->where('class', 'publishing')->where('organization', \Session::get('organization'))->orderBy('sequence', 'ASC')->where('sequence', $id)->first();

            $status->status = getTranslation($status->status, $lang);
            
            return response() -> json([ 'status' => $status, 'users' => $users ]);
        }
        
        $status->status = getTranslation($status->status, $lang);

        return response() -> json([ 'status' => $status, 'users' => $users ]);

    }
    
    public function getList( Request $request ) {
        
        $lang   = $request->input( 'lang' );
        $page   = $request->input( 'page' );
        $search   = $request->input( 'search' );
        $filter = $request->input( 'filter' );
        $filterValue = $request->input('filter_value');

        $statuses = $this -> get_status( $lang );
        // dd($statuses);
        if($statuses == null) { return response() -> json( [ 'statuses' => $statuses]  ); }

        $publishings = $this -> show_data( $lang, $page, $search, $filter, $filterValue);

        // $tags = $this -> get_tags ( $lang );

        // $platforms = $this -> getPlatforms( $lang );

        

        return response() -> json( [ 'publishings' => $publishings['publishingsArr'], 'statuses' => $statuses, 'totalRecords' => $publishings['totalRows'],'page' => $page]  );

    }

    public function getPublishingName( Request $request, $id, $lang ) {

        $publishing = Publishing::whereId( $id ) -> first();

        $message = ucwords( string_to_value( $lang, $publishing -> name) );

        return response() -> json( [ 'name' => $message ] );

    }

    public function show_data( $lang, $page, $search, $filter, $filterValue) {
        
        $publishings = Publishing::selectRaw( "id, name, item_id" ) ->with([
            'item' => function($query) {
                $query->select('id','item_name', 'content', 'organization');
            },
            'publishing_histories',
            'publishing_histories.platformItem.brand_object' => function($query) {
                $query->select('id','name');
            },
            'publishing_histories.platformItem.platformType' => function($query) {
                $query->select('id','name');
            },
            'publishing_histories.dateStatus' => function($query) {
                $query->select('id','status','sequence');
            },
            'publishing_histories.publisher_object' => function($query) {
                $query->select('id','first_name','last_name');
            },
            'publishing_histories.author_object' => function($query) {
                $query->select('id','first_name','last_name');
            },
            'publishing_relation' => function($query) {
                $query->select('id','linked_publishing','publishing', 'status');
            },
        ]);
        
        if($search != '') {
            $publishings = $publishings->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere(function($subQuery) use ($search){   
               
                $subQuery->whereHas('item', function ( $query ) use ($search) {
                    $query->where('item_name',  'LIKE', '%' .$search . '%' );
                })
                ->orWhereHas('publishing_histories.platformItem.brand_object', function ( $query ) use ($search) {
                    $query->where('name',  'LIKE', '%' . $search . '%' );
                })
                ->orWhereHas('publishing_histories.platformItem.platformType', function ( $query ) use ($search) {
                    $query->where('name',  'LIKE', '%' . $search . '%' );
                })
                ->orWhereHas('publishing_histories.dateStatus', function ( $query ) use ($search) {
                    $query->where('status',  'LIKE', '%' .  $search. '%' );
                })
                ->orWhereHas('publishing_histories.publisher_object', function ( $query ) use ($search) {
                    $query->whereRaw("concat(last_name, ' ', first_name) like '%$search%' ");
                })
                ->orWhereHas('publishing_histories.author_object', function ( $query ) use ($search) {
                    $query->whereRaw("concat(last_name, ' ', first_name) like '%$search%' ");
                });
            })->get();
            
        } else if($filterValue != '') {
            
            if($filter == 'name') $publishings = $publishings->where('name', 'LIKE', '%' . $filterValue . '%')->get();
            if($filter == 'status') {
                $publishings = $publishings->get();
                $publishings = collect($publishings)->filter(function ($publishing) use ($filterValue) {
                    return false !== stripos($publishing->publishing_histories->dateStatus->status, $filterValue);
                });
            }
            if($filter == 'author') $publishings = $publishings->where(function($subQuery) use ($filterValue){   
                $subQuery->whereHas('publishing_histories.author_object', function ( $query ) use ($filterValue) {
                    $query->whereRaw("concat(last_name, ' ', first_name) like '%$filterValue%' ");
                })->orWhereHas('publishing_histories.publisher_object', function ( $query ) use ($filterValue) {
                    $query->whereRaw("concat(last_name, ' ', first_name) like '%$filterValue%' ");
                });
            })->get();
            
        } else {
            $publishings = $publishings->get();
        }
        // dd($publishings);
        $organization = \Session::get('organization');
            
        $publishings = $publishings->where('item.organization', $organization);

        $publishings = $publishings->groupBy('publishing_histories.platformItem.brand_object.name');
        
        $tableArr = array();

        $publishingsArr = array();

        $countArr = array();
        
        foreach( $publishings as $key => $publishing ) {
       
            array_push( $countArr, $publishing->count());
            $publishing = collect($publishing);
            foreach ($publishing->forPage($page,5) as $datas) {
                // dd($datas->publishing_relation->where('organization', \Session::get('organization'))->toArray());
                $publishingName = getTranslation( $datas->name , $lang );
                $itemName = getTranslation($datas->item->item_name, $lang);
                $dateStatus = getTranslation($datas->publishing_histories->dateStatus->status, $lang);
                $platformName = getTranslation($datas->publishing_histories->platformItem->platformType->name, $lang);
                // $numberOfToBeLinked = collect($datas->publishing_relation)->where()->map(function ($array)) {
                //     return collect($array)->unique('id')->all();
                // }
                $numberOfToBeLinked = $this->publishingRelation($datas->publishing_relation)->unique(function ($item) {
                    return $item['linked_publishing'];
                })->reject(function ($value, $key) {
                    return $value->status === "Link Created";
                })->count();

                // dd($numberOfToBeLinked->toArray());
                array_push( $publishingsArr, [ 
                    'id'                => $datas[ 'id' ],
                    'publishing_name'   => $publishingName,
                    'status_id'         => $datas->publishing_histories->dateStatus->id,
                    'author_id'         => $datas->publishing_histories->author,
                    'item_id'           => $datas->item->id,
                    'item_name'         => $itemName,
                    'content_id'        => $datas->item->content,
                    'tags'              => $datas->publishing_tags != null ? $datas->publishing_tags : '',
                    'status_name'       => $dateStatus,
                    'brand_logo'        => $datas->publishing_histories->platformItem->brand_object->name,
                    'date_sequence'     => $datas->publishing_histories->dateStatus->sequence,
                    'publisher_name'    => $datas->publishing_histories->publisher_object->full_name,
                    'author_name'       => $datas->publishing_histories->author_object->full_name,
                    'platform_name'     => $platformName,
                    'to_be_linked'      => $numberOfToBeLinked,
                    'platform_id'       => $datas->publishing_histories->platform,
                    'date'              => date( 'F d, Y', strtotime( $datas->publishing_histories->updated_at ) ),
                    'isHovering'        => false,
                ] );
            }
            array_push( $tableArr, [ 
                
                'mode'      => 'span', 
                'label'     => $key, 
                'html'      => false, 
                'children'  => $publishingsArr,

            ] );
            
            $publishingsArr = array();
        }
        // dd(count($countArr) );
        $datas = [
            'totalRows' => count($countArr) != 0 ? max($countArr) : 0,
            'publishingsArr' => $tableArr
        ];
        
        return $datas;

    }

    public function get_status( $lang ) {

        
        $status = DateStatus::select( 'id' , 'status', 'sequence' )->where('class', 'publishing')->where('organization', \Session::get('organization'))->orderBy('sequence', 'ASC')->first();
       
        if($status == null) { return $status; }
        
        $status->status = getTranslation($status->status, $lang);
        
        return $status;
        
    }

    public function get_tags( $lang ) {

        $tags = Tags::select('id','name')->get();

        $tagsArr = array();

        foreach ($tags as $tag ) {
            
            $tagName = getTranslation($tag->name, $lang);

            array_push( $tagsArr, [
                
                'id' => $tag->id,

                'name' => $tagName,

            ] );
        }

        return $tagsArr;
        
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

    public function get_items( $lang ) {
        
        $organization = \Session::get('organization');

        $items = Item::selectRaw( "id, item_name, status, organization" )->where('organization', $organization) -> get(); 
        
        $dateStatus = DateStatus::select( 'id' , 'status', 'sequence' )->where('class', 'item')->where('organization', $organization)->orderBy('sequence', 'DESC')->first();
        // dd($items->toArray());

        $itemsArr = array();
        
        foreach( $items as $item ) {
            if($item->status == $dateStatus->id) {
                $itemName = getTranslation($item->item_name, $lang);

                array_push( $itemsArr, [

                        'id'                => $item[ 'id' ],

                        'item_name'         => $itemName,         

                    ] 
                );
            }

        }

        return $itemsArr;
        
    }

    public function getPlatforms( $lang ) {

        $platforms = PlatformItem::selectRaw( "id, brand, platform_type") ->with(
            
            [
                'brand_object' => function($query) {
                    $query->select('id','logo','name');
                },
                'platformType' => function($query) {
                    $query->select('id','name');
                },
            ]
        )-> get();

        $platformsArr = [];

        foreach( $platforms as $platform ) {
            
            array_push( $platformsArr, [

                'platform_id' => $platform->id,

                'platform_name' => $platform->platform_item_name,

            ] );

        }
     
        return $platformsArr;

    }

    public function getPlatformTypes( $lang ) {

        $platform_type = PlatformType::orderBy( 'id', 'DESC' ) -> get();

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

    public function getBrands( $lang ) {

        $brands = Brand::selectRaw( "id, name" ) -> get();

        return $brands;
        
    }

    public function getNamePlatformType( $id, $lang ) {
        
        $platform_type = PlatformType::select('name')->whereId( $id ) -> first();

        $name = getTranslation($platform_type->name, $lang);

        return $name;

    }

    public function getUsersAndItems(Request $request) {

        $lang = $request -> input( 'lang' );

        $users = $this -> get_users( $lang );

        $items = $this -> get_items( $lang );

        $platforms = $this -> getPlatforms( $lang );

        return response() -> json( [ 'users' => $users, 'items' => $items, 'platforms' => $platforms]  );

    }

    public function publishingRelation($publishing) {

        $organization = \Session::get('organization');
        
        return $publishing->where('organization', $organization);
    }

    public function getPublishingRelation(Publishing $publishing, Request $request) {

        $lang = $request -> input( 'lang' );

        $publishing->load(['publishing_relation' => function ($query) {
            $query->select('id', 'publishing', 'linked_publishing', 'status');
        }]);
        // dd($publishing);
        $publishingRelation = $this->publishingRelation($publishing->publishing_relation)->unique(function ($item) {
            return $item['linked_publishing'];
        })->reject(function ($value, $key) {
            return $value->status === "Link Created";
        });
        // dd($TEST);
        return response() -> json( [ 'publishing_relation' => $publishingRelation]  );

    }

}
