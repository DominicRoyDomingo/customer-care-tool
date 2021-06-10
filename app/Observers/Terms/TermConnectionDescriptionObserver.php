<?php

namespace App\Observers\Terms;

use App\Models\MedicalStuff\TermConnectionDescription;
use DB;

class TermConnectionDescriptionObserver
{
   public function created(TermConnectionDescription $termDesc)
   {
      if (!request()->is_mutual) {
         return null;
      }

      DB::beginTransaction();
      try {
         DB::table('term_connection_descriptions')
            ->insert([
               'term_id' => $termDesc->linked_term_id,
               'linked_term_id' => $termDesc->term_id,
               'description_id' => request()->is_mutual ?  $termDesc->description_id : null,
               'value' => $termDesc->value,
               'note' => $termDesc->note,
               'method' => $termDesc->method,
               'created_at' => now(),
            ]);
         DB::commit();
      } catch (\Exception $e) {
         DB::rollBack();
      }
   }

   public function updated(TermConnectionDescription $termDesc)
   {
      // dd($termDesc);
      if (!request()->is_mutual) {
         return null;
      }

      DB::beginTransaction();
      try {

         $table = DB::table('term_connection_descriptions');

         if (request()->method === 'mutual') {
            $table->insert([
               'term_id' => $termDesc->linked_term_id,
               'linked_term_id' => $termDesc->term_id,
               'description_id' => request()->is_mutual ?  $termDesc->description_id : null,
               'value' => $termDesc->value,
               'note' => $termDesc->note,
               'method' => $termDesc->method,
               'created_at' => now(),
            ]);
         } else {
            $table->where('term_id', $termDesc->linked_term_id)
               ->where('linked_term_id', $termDesc->term_id)
               ->update([
                  'description_id' => $termDesc->description_id,
                  'value' => $termDesc->value,
                  'note' => $termDesc->note,
                  'method' => $termDesc->method,
               ]);
         }
         DB::commit();
      } catch (\Exception $e) {
         DB::rollBack();
      }
   }

   public function deleting(TermConnectionDescription $termDesc)
   {
      DB::beginTransaction();
      try {

         DB::table('term_connection_descriptions')
            ->where('term_id', $termDesc->linked_term_id)
            ->where('linked_term_id', $termDesc->term_id)
            ->delete();

         DB::commit();
      } catch (\Exception $e) {
         DB::rollBack();
      }
   }
}
