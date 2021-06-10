<?php


namespace CRM\API\MedicalStuff\Traits;

use App\Helpers\Algolia;
use App\Helpers\General\CollectionHelper;
use App\Models\MedicalStuff\MedicalTerm;
use DB;

/**
 * Terms Items
 */
trait ArticleTrait
{
   public function store_item_type_link($articleId,  $array = [])
   {
      DB::beginTransaction();

      try {
         DB::table('publishing_item_type_link')->where('article_id', $articleId)->delete();

         foreach ($array as $id) {
            DB::table('publishing_item_type_link')
               ->insert([
                  'article_id' => $articleId,
                  'publishing_item_type_id' => $id,
                  'created_at' => carbon()
               ]);
         }
         DB::commit();
      } catch (\Exception $e) {
         DB::rollBack();
      }
   }

   public function store_course_info($article, $data = [])
   {
      if (request()->action === 'Update') {
         $article->course_info()->delete();
      }

      foreach ($data as $course) {
         if (!empty($course->course_types)) {
            $courseType = json_encode($course->course_types);
            if (isset($course->course_types[0]->id)) {
               $ctIds = [];
               foreach ($course->course_types as $ct) {
                  $ctIds[] = $ct->id;
               }
               $courseType = json_encode($ctIds);
            }

            $article
               ->course_info()
               ->create([
                  // 'course_id' => $courseId,
                  'price' => $course->price,
                  'number_credit' => $course->credit,
                  'time_duration' => $course->hours,
                  'address' => $course->address,
                  'course_types' => $courseType,
                  'language' => $course->language
                  // 'course_types' => json_encode($course->course_types)
               ]);
         }
      }
   }

   public function checkItemTypeForSyncing($items, $article){
      if(in_array(27, $article->publishing_item_type_articles->pluck('id')->toArray())){ 
         if(self::checkItemTypeChanged(27, $article->publishing_item_type_articles->pluck('id')->toArray(), $items)){
           return Algolia::delete_syncreference('Course', 'medical_articles', $article->id);
         }
      }

      if(in_array(27, $items)){
         ($article->course_sync_reference)
         ? Algolia::updateSyncReference($article->course_sync_reference)
         : Algolia::create_syncreference('Course', 'medical_articles', $article->id);
      };
   }


   //Check if the Item IS REMOVED
   protected static function checkItemTypeChanged($id, $array, $request_array){
      if(in_array($id,$array)){
         if(!in_array($id, $request_array)){
            return true;
         }
      }
   }

   public function store_course_other_info($article, $data = [])
   {
      if (request()->action === 'Update') {
         $article->course_information_types()->delete();
      }

      foreach ($data as $infoType) {
         if ($infoType->information_type) {
            $article
               ->course_information_types()
               ->create([
                  // 'course_id' => $courseId,
                  'information_type_id' => $infoType->information_type,
                  'information' => $infoType->information
               ]);
         }
      }
   }
}
