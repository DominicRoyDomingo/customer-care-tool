<?php

namespace CRM\API\MedicalStuff\Traits;


/**
 * Search Trait for search controller
 */
trait SearchTrait
{

   public function get_advance_search_term($paginated)
   {
      $request = request();

      return $paginated
         ->getCollection()
         ->map(function ($term) use ($request) {
            if ($request->type_terms && $request->specializations) {
               if ($this->check_term_types($term,  $request->type_terms) && $this->check_specializations($term,  $request->specializations)) {
                  return $term;
               }
            } else if (is_array($request->type_terms) && $this->check_term_types($term, $request->type_terms)) {
               return $term;
            } else if (is_array($request->specializations) && $this->check_specializations($term, $request->specializations)) {
               return $term;
            }

            if (is_array($request->terms) && $this->check_terms($term, $request->terms)) {
               return $term;
            }

            if (is_array($request->articles) && $this->check_articles($term, $request->articles)) {
               return $term;
            }
         })
         ->filter(function ($value) {
            return $value;
         })
         ->values();
   }

   public function check_term_types($term, array $data)
   {
      $k = false;
      foreach ($data as $ttId) {
         $typeIdArray = string_to_value('term_types', $term->term_types);
         if ($typeIdArray && in_array($ttId,  $typeIdArray)) {
            $k = true;
         }
      }
      return $k;
   }

   public function check_terms($term, array $data)
   {
      $k = false;
      foreach ($data as $termId) {
         $termIdArray = json_decode($term->medical_terms) ?? null;
         if (is_array($termIdArray) && in_array($termId,  $termIdArray)) {
            $k = true;
         }
      }
      return $k;
   }

   public function check_specializations($term, array $data)
   {
      $k = false;
      foreach ($data as $specId) {
         $specIdArray = string_to_value('specializations', $term->specializations);
         if ($specIdArray && in_array($specId,  $specIdArray)) {
            $k = true;
         }
      }
      return $k;
   }

   public function check_articles($term, array $data)
   {
      $k = false;

      foreach ($data as $articleId) {
         if ($articles = $term->medical_articles) {
            foreach ($articles as $article) {
               if ((int)$articleId === $article->id) {
                  $k = true;
               }
            }
         }
      }
      return $k;
   }




   // ========>

   public function get_advance_search_article($data = [])
   {
      $request = request();

      return collect($data)
         // ->getCollection()
         ->map(function ($article) use ($request) {

            if (is_array($request->terms) && $this->check_article_terms($article, $request->terms)) {
               return $article;
            }

            // if ($request->type_terms && $request->specializations) {
            //    if ($this->check_term_types($term,  $request->type_terms) && $this->check_specializations($term,  $request->specializations)) {
            //       return $term;
            //    }
            // } else if (is_array($request->type_terms) && $this->check_term_types($term, $request->type_terms)) {
            //    return $term;
            // } else if (is_array($request->specializations) && $this->check_specializations($term, $request->specializations)) {
            //    return $term;
            // }

            // if (is_array($request->terms) && $this->check_terms($term, $request->terms)) {
            //    return $term;
            // }

            // if (is_array($request->articles) && $this->check_articles($term, $request->articles)) {
            //    return $term;
            // }
         })
         ->filter(function ($value) {
            return $value;
         })
         ->values();
   }

   public function check_article_terms($article, $terms)
   {
      foreach ($article->medical_terms as $term) {
      }
   }
   // public function check_article_terms()
   // {
   // }


   /****
    * THIS FUNCTION BELOW IS INTENDED FOR ARTICLE
    * FOR ANY REVISION ANY FUNCTION OR FUNCTIONS
    * BELOW PLEASE MESSAGE THE OWNER
    * Created By: Junril Pateño
    ****/

   /**** START FUNCTION ****/
   public function check_terms_for_article($term, array $data)
   {
      $k = false;
      $array = [];
      foreach ($data as $termId) {
         foreach ($term as $terms) {
            // dd( $termId );
            // dd( $terms->pivot->medical_term_id );
            // if ($termIdArray = json_decode($terms->medical_terms)) {
            //    foreach ($termIdArray as $tId) {
            //       if ((int)$termId === (int) $tId) {
            //          $k = true;
            //          dd((int) $tId );
            //       }
            //    }
            // }
            if ((int)$termId === $terms->pivot->medical_term_id) {
               $k = true;
            }
         }
      }
      return $k;
   }

   public function check_author_article($term, array $data)
   {
      $k = false;
      foreach ($data as $authorId) {
         foreach ($term as $terms) {
            if ($author = json_decode($terms->authors)) {
               foreach ($author as $authors) {
                  if ((int)$authorId === $authors) {
                     $k = true;
                  }
               }
            }
            if ((int)$authorId === $terms->id) {
               $k = true;
            }
         }
      }
      return $k;
   }

   public function check_type_date_article($term, $data)
   {
      $k = false;

      foreach ($data->type_dates as $type_date) {
         foreach ($term as $terms) {
            if ((int)$type_date === $terms->id) {
               if (!empty($data->from) &&  !empty($data->to)) {
                  if (date('Y-m-d', strtotime($data->from)) <=  $terms->pivot->article_date &&  date('Y-m-d', strtotime($data->to)) >=  $terms->pivot->article_date) {
                     $k = true;
                  }
               } elseif (!empty($data->from) &&  empty($data->to)) {
                  if (date('Y-m-d', strtotime($data->from)) <=  $terms->pivot->article_date) {
                     $k = true;
                  }
               } elseif (empty($data->from) &&  !empty($data->to)) {
                  if (date('Y-m-d', strtotime($data->to)) >=  $terms->pivot->article_date) {
                     $k = true;
                  }
               } else {
                  $k = true;
               }
            }
         }
      }
      return $k;
   }
   /**** END FUNCTION ****/
}
