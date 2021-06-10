<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageValidator extends Model
{
    
  public function get_data( $lang = '', $fields, $items = array() ) {
    
    $data = [];
    
    if ( ! empty( $items ) ) {
      
      foreach ( $items as $ik => $item ) {
        
        $key = false;
       
        $object = [];

        foreach ( $fields as $field ) {
          
          $array = $this -> to_array( $item -> $field );
          // dd(is_array( $array ));
          if ( is_array( $array ) ) {
            
            foreach ( $array as $ak => $arr ) {
              
              if ( $lang === $ak ) {

                $object[ $field ] = $arr;

                $key = true;
                
              }

              if ( $lang === '' or $lang === null ) {

                $object[ $field ] = $arr;

                $key = true;

              }
              
              if(is_array($arr)) {

                if(count($arr) > 1) {

                  foreach($arr as $ak => $ar) {
                    
                    if($ak == $field || $ak == 'id'){

                      if($ak == 'id'){

                        $object[ 'author_id' ] = $ar;
                        
                        $key = true;

                      }else {
                        
                        $object[ $field ] = $ar;

                        $key = true;

                      }
                      
                    }

                  }

                } else {
                  
                  foreach($arr as $ar) {

                    $object[ $field ] = $ar;

                    $key = true;

                  }

                }

              }

            }

          } else {
            
            $object[ $field ] = $item -> $field;

          }

        }

        if ( $key ) {

          $data[] = $object;

        }

      }

    }
    
    return (object) $data;

  }
    
  
  public function to_array( $string )  {

      $arr = json_decode( $string, true );

      return  $arr;

  }
      
}
