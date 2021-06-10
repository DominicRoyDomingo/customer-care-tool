<?php

namespace CRM\API\ContactType;

use App\Http\Controllers\Controller;
use App\Models\ContactType;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContactTypeController extends Controller
{
    private $contactTypeRepository;
 
    public function __construct(ContactTypeRepository $contactTypeRepository)
    {
       $this->contactTypeRepository = $contactTypeRepository;
    }
 
    public function index(Request $request)
    {
       $contact_types = $this->contactTypeRepository->getAll($request);

       
         if (!$request->sortbyLang || $request->sortbyLang === 'all') {
            $lang = $request -> input( 'lang' );
         } else {
            $lang = $request->sortbyLang;
         }
        $language = 'English';

         if( $lang == 'it') { $language = 'Italian'; } 
         if( $lang == 'de' ) { $language = 'German'; } 
         if( $lang == 'ph-fil' ) { $language = 'Filipino'; }
         if( $lang == 'ph-bis' ) { $language = 'Visayan'; }
        
        $contact_types_array = array();

        foreach( $contact_types as $datas ) {

            $name = $this -> getName( $datas[ 'id' ], $lang );
            
            $base_name = ( ! empty( $name ) ? $name : $this -> getName( $datas[ 'id' ], 'en' ) );
            
            if( $name == '' && $base_name == '' ) {

                $base_name_italy = $this -> getName( $datas[ 'id' ], 'it' );

                if( $base_name_italy !== '' ) {

                    $base_name = $base_name_italy;

               } else {

                  $base_name_germany = $this -> getName( $datas[ 'id' ], 'de' );

                  if( $base_name_germany !== '' ) {

                     $base_name = $base_name_germany;
 
                  }

                  else 

                  {

                     $base_name_filipino = $this -> getName( $datas[ 'id' ], 'ph-fil' );

                     if( $base_name_filipino !== '' ) {

                        $base_name = $base_name_filipino;
   
                     }

                     else{

                        $base_name = $this -> getName( $datas[ 'id' ], 'ph-bis' );
                        
                     }

                  }

               }
                
            }
            //$datas[ 'type_name' ] = $base_name;
            
            if (!$request->sortbyLang || $request->sortbyLang === 'all' ) {
               $datas[ 'localized_type_name' ] = $base_name;
               $datas[ 'convertion' ] = ( ! empty( $name ) ? false : true );
               $datas[ 'language' ] = $language;
               array_push( $contact_types_array, $datas);
            } else {
               if( empty( $name ) ) {
                  $datas[ 'localized_type_name' ] = $base_name;
                  $datas[ 'convertion' ] = ( ! empty( $name ) ? false : true );
                  $datas[ 'language' ] = $language;
                  array_push( $contact_types_array, $datas);
               }
            }
            

        }
        
       return response()->json($contact_types_array);
    }

   public function store(Request $request)
   {
      $request->validate([
         'type_name' => 'required|min:2'
      ]);

      if (is_null($request->id)) {
         $sameContactType = ContactType::where('type_name', $request->type_name)->first();

         if ($sameContactType != null) {
            throw ValidationException::withMessages(['request' => $request->type_name . ' already exists']);
         }

         $this->contactTypeRepository->create($request->only('type_name'));
      } else {
         $this->contactTypeRepository->update($request->id, $request->only('type_name', 'locale'));
      }

      return response()->json(true);
   }

   public function destroy(Request $request)
   {
      $contact_type = $this->contactTypeRepository->getById($request->id);

      if ($contact_type->contacts->count() > 0) {
         throw ValidationException::withMessages(['contact_type' =>  strtoupper(
            $contact_type->contact_type_name
         ) . ' is used and cannot be deleted.']);
      }

      $contact_type->delete();

      return response()->json(true);
   }
   
   public function getName( $id, $lang ) {
        
      $contacts = $this->contactTypeRepository->getById($id);
      
      $name = ucwords( string_to_value( $lang, $contacts -> type_name) );
      
      return $name;

  }

}
