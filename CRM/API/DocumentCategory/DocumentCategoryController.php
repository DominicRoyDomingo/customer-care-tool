<?php

namespace CRM\API\DocumentCategory;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\Category;
use App\Models\CategoryDocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DocumentCategoryController extends Controller
{
   private $documentCategoryRepository;

   public function __construct(DocumentCategoryRepository $documentCategoryRepository)
   {
      $this->documentCategoryRepository = $documentCategoryRepository;
   }

   public function index(Request $request)
   {
      

      if (!$request->sortbyLang || $request->sortbyLang === 'all') {
         $lang = $request->locale;
      } else {
         $lang = $request->sortbyLang;
      }

      $language = 'English';

      if( $lang == 'it') { $language = 'Italian'; } 
      if( $lang == 'de' ) { $language = 'German'; } 
      if( $lang == 'ph-fil' ) { $language = 'Filipino'; }
      if( $lang == 'ph-bis' ) { $language = 'Visayan'; }
      
      $requestType = DocumentCategory::get();

      $documentCategoryArray = array();

        foreach( $requestType as $datas ) {
         
            $name = $this -> getNameDocumentCategory( $datas[ 'id' ], $lang );
            
            $base_name = ( ! empty( $name ) ? $name : $this -> getNameDocumentCategory( $datas[ 'id' ], 'en' ) );

            if( $name == '' && $base_name == '' ) {

                $base_name_italy = $this -> getNameDocumentCategory( $datas[ 'id' ], 'it' );

                if( $base_name_italy !== '' ) {

                    $base_name = $base_name_italy;

                } else {

                  $base_name_germany = $this -> getNameDocumentCategory( $datas[ 'id' ], 'de' );

                  if( $base_name_germany !== '' ) {

                     $base_name = $base_name_germany;
 
                  }

                  else 

                  {

                     $base_name_filipino = $this -> getNameDocumentCategory( $datas[ 'id' ], 'ph-fil' );

                     if( $base_name_filipino !== '' ) {

                        $base_name = $base_name_filipino;
   
                     }

                     else{

                        $base_name = $this -> getNameDocumentCategory( $datas[ 'id' ], 'ph-bis' );
                        
                     }

                  }

                }
                
            }
            
            if (!$request->sortbyLang || $request->sortbyLang === 'all' ) {
               array_push( $documentCategoryArray, [
                  'id' => $datas[ 'id' ],
                  'document_category_name' => $base_name,
                  'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),
                  'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),
                  'convertion' => ( ! empty( $name ) ? false : true ),
                  'language' => $language
               ] );
            } else {
               if( empty( $name ) ) {
                  array_push( $documentCategoryArray, [
                  'id' => $datas[ 'id' ],
                  'document_category_name' => $base_name,
                  'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),
                  'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),
                  'convertion' => ( ! empty( $name ) ? false : true ),
                  'language' => $language
                  ] );
               }

            }

        }
      // $documentCategories = $this->documentCategoryRepository->getAll($request);

      return response()->json($documentCategoryArray);
   }

   public function getNameDocumentCategory( $id, $lang ) 
   {
        
      $document_name = DocumentCategory::whereId( $id ) -> first();

      $name = ucwords( string_to_value( $lang, $document_name -> name) );

      return $name;

  }

   public function show(Request $request, $id)
   {
      $documentCategory = $this->documentCategoryRepository->getById($id);
      $documentCategory->document_types = $documentCategory->document_types;

      return response()->json($documentCategory);
   }

   // for Category here
   public function store(Request $request)
   {
      $documentCategory = null;
      if ( empty( $request->id ) ) {
         $request->validate([
            'name' => 'required|min:2|unique:document_categories,name->' . locale()
         ]);

         $documentCategory = $this->documentCategoryRepository->create($request->all());
      } else {
         $request->validate([
            'name' => 'required|min:2|unique:document_categories,name->' . locale()
         ]);

         $documentCategory = $this->documentCategoryRepository->update($request->id, $request->all());
      }

      $documentCategory->responseStatus = true;

      return response()->json($documentCategory);
   }

   public function destroy(Request $request)
   {
      $documentCategory = $this->documentCategoryRepository->getById($request->id);

      if ($documentCategory->document_types->count() > 0) {
         throw ValidationException::withMessages(['documentCategory' =>  strtoupper(
            $documentCategory->document_category_name
         ) . ' is used and cannot be deleted.']);
      }

      if ($documentCategory->attachments > 0) {
         throw ValidationException::withMessages(['documentCategory' =>  strtoupper(
            $documentCategory->document_category_name
         ) . ' is used and cannot be deleted.']);
      }

      $documentCategory->delete();

      return response()->json(true);
   }
}
