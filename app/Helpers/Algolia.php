<?php

namespace App\Helpers;

use App\Models\{ItemType, BrandArticle};
use App\Models\SyncReference;
use Illuminate\Http\Request;

class Algolia
{
    /**
     * Create Sync Reference
     * @param sync_class Article Type
     * @param source_table Table of referencing type
     * @param table_id PK from Source Table
    */
   public static function create_syncreference($sync_class, $source_table, $table_id){
    if(!self::isSyncReferencePresent($sync_class, $source_table, $table_id)){
        SyncReference::create([
            "sync_class" => $sync_class, 
            "action" => 'New',
            "source_table" => $source_table,
            "table_id" => $table_id
        ]);
    }
       
   }

    public static function delete_syncreference($sync_class, $source_table, $table_id){
        $sync = self::isSyncReferencePresent($sync_class, $source_table, $table_id);
        ($sync)? $sync->delete() : "";
    }

   /**
     * Update Article Type Sync Reference
     * @param type Article Type
    */
   public static function update_articletype_syncreference($type, $brand){
       $numAdded = 0;
       $numUpdated = 0;
       $numNew = 0;
       
       $table = SyncReference::where('source_table', 'medical_articles')->get();
       $itemType = ItemType::findOrFail($type);

       $sync_class = $itemType->is_english;   
       
       //request type for model->searchable()
       request()->merge(["algolia_syncType" => $sync_class]);
    
       foreach($itemType->articles as $article){
           if(self::isArticlePresent($brand, $article->brands)){

            $articleID = $article->id;

            $reference = $table->where('table_id',  $articleID)->where('sync_class', $sync_class)->first();

            if($reference){
                if($reference->action == 'Update'){
                    $article->searchable();
                    $numUpdated++;
                }elseif($reference->action == "New"){
                    $article->searchable();
                    $numNew++;
                }
                $reference->update(['action' => 'Added']);
            }else{
                self::create_syncreference($sync_class,'medical_articles',$articleID);
                $numAdded++;
            }
           }
       }; 

       $sync_log = ['added' => $numNew, 'updated' => $numUpdated, 'new' => $numAdded];

       return $sync_log;
   }

   protected static function isArticlePresent($brand, $object){
       foreach($object as $obj){
            if($obj->id == $brand){
                return true;
            };
       }
   }

   protected static function isSyncReferencePresent($sync_class, $source_table, $table_id){
    return SyncReference::where('table_id',  $table_id)->where('sync_class', $sync_class)->where('table_id', $table_id)->first();
}

   public static function loadAlgoliaSummary(Request $request){
    $new = 0;
    $update = 0;

    if($request->table != null && $request->class != null){
        $articles = BrandArticle::where('brand_id', $request->brand_id)->get();

        foreach($articles as $article){
            $table = SyncReference::where('source_table', $request->table)->where('sync_class', $request->class)->where('table_id', $article->article_id)->first();
            if($table){
                ($table->action == 'New') 
                ? $new++ 
                : ( 
                    ($table->action == 'Update') 
                    ? $update++ 
                    : ''
                ); 
            }    
        }
    }

    return response()->json([
        'new' => $new,
        'update' => $update,
    ]);
   }

   public static function updateSyncReference($object){
        return ($object->action == "New") 
        ? "" 
        : $object->update([
            "action" => "Update"
        ]);
   }
}
