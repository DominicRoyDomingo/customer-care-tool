<?php

namespace CRM\API\DocumentType;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\Category;
use App\Models\CategoryDocumentType;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DocumentTypeController extends Controller
{
   private $documentTypeRepository;

   public function __construct(DocumentTypeRepository $documentTypeRepository)
   {
      $this->documentTypeRepository = $documentTypeRepository;
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
         
         $document_type = DocumentType::get();
   
         $documentTypeArray = array();

         foreach( $document_type as $datas ) {
         
            $name = $this -> getNameDocumentType( $datas[ 'id' ], $lang );

            $base_name = ( ! empty( $name ) ? $name : $this -> getNameDocumentType( $datas[ 'id' ], 'en' ) );

            if( $name == '' && $base_name == '' ) {

               $base_name_italy = $this -> getNameDocumentType( $datas[ 'id' ], 'it' );

               if( $base_name_italy !== '' ) {

                  $base_name = $base_name_italy;

               } else {

                  $base_name_germany = $this -> getNameDocumentType( $datas[ 'id' ], 'de' );

                  if( $base_name_germany !== '' ) {

                     $base_name = $base_name_germany;
 
                  }

                  else 

                  {

                     $base_name_filipino = $this -> getNameDocumentType( $datas[ 'id' ], 'ph-fil' );

                     if( $base_name_filipino !== '' ) {

                        $base_name = $base_name_filipino;
   
                     }

                     else{

                        $base_name = $this -> getNameDocumentType( $datas[ 'id' ], 'ph-bis' );
                        
                     }

                  }

               }

            }

            $document_category = $this -> getNameDocumentCategory( $datas[ 'document_category_id' ], $lang );

            $base_document_category = ( ! empty( $document_category ) ? $document_category : $this -> getNameDocumentCategory( $datas[ 'document_category_id' ], 'en' ) );
            
            if( $document_category == '' && $base_document_category == '' ) {

               $document_category_base_name_italy = $this -> getNameDocumentCategory( $datas[ 'document_category_id' ], 'it' );
              
               if( $document_category_base_name_italy !== '' ) {

                  $base_document_category = $document_category_base_name_italy;

               } else {

                  $base_name_germany = $this -> getNameDocumentCategory( $datas[ 'document_category_id' ], 'de' );

                  if( $base_name_germany !== '' ) {

                     $base_document_category = $base_name_germany;
 
                  }

                  else 

                  {

                     $base_name_filipino = $this -> getNameDocumentCategory( $datas[ 'document_category_id' ], 'ph-fil' );

                     if( $base_name_filipino !== '' ) {

                        $base_document_category = $base_name_filipino;
   
                     }

                     else{

                        $base_document_category = $this -> getNameDocumentCategory( $datas[ 'document_category_id' ], 'ph-bis' );
                        
                     }

                  }

                }
               
            }

            $array_document_category = "";

            $doc_cat = DocumentCategory::where( 'id', '=', $datas[ 'document_category_id' ] ) -> first();
            $array_document_category = [
                  'id' => $doc_cat -> id,
                  'document_category_name' => ( ! empty( $this -> getNameDocumentCategory( $datas[ 'document_category_id' ], $lang ) ) ? $this -> getNameDocumentCategory( $datas[ 'document_category_id' ], $lang ) : $base_document_category ),
                  'name' => $doc_cat -> name,
                  'created_at' => $doc_cat -> created_at,
                  'updated_at' => $doc_cat -> updated_at
            ];

            if (!$request->sortbyLang || $request->sortbyLang === 'all' ) {
               

               array_push( $documentTypeArray, [

                  'id' => $datas[ 'id' ],

                  'name' => $datas[ 'name' ],

                  'document_type_name' => $base_name,

                  'document_type_full_name' => $base_name . ': ' .$base_document_category,

                  'document_category_id' => $datas[ 'document_category_id' ],

                  'document_category' => $array_document_category,

                  'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),

                  'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),

                  'convertion' => ( ! empty( $name ) ? false : true ),

                  'convertion_document_category' => ( ! empty( $document_category ) ? false : true ),

                  'language' => $language

               ] );
            } else {
               if( empty( $name ) ) {
                     if( ! empty( $document_category ) && $request -> lang != 'en' ) {
                        $documentCategoryName =  $document_category;
                        $tf = false;
                     } else {
                        $documentCategoryName = $this -> getNameDocumentCategory( $datas[ 'document_category_id' ], 'en' );
                        $tf = true;
                     }  
                     if( $request -> lang == 'en' ) {
                        $tf = '';
                     }
                  
                  $array_document_category = [
                     'id' => $doc_cat -> id,
                     'document_category_name' => $documentCategoryName,
                     'name' => $doc_cat -> name,
                     'created_at' => $doc_cat -> created_at,
                     'updated_at' => $doc_cat -> updated_at
                  ];

                  array_push( $documentTypeArray, [

                     'id' => $datas[ 'id' ],
   
                     'name' => $datas[ 'name' ],

                     'document_type_name' => $base_name,

                     'document_type_full_name' => $base_name . ': ' .$base_document_category,

                     'document_category_id' => $datas[ 'document_category_id' ],

                     'document_category' => $array_document_category,
                     
                     'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),
   
                     'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),
   
                     'convertion' => ( ! empty( $name ) ? false : true ),
   
                     'convertion_document_category' => $tf,

                     'language' => $language
   
                  ] );
               }
            }

        }

      $documentTypes = $this->documentTypeRepository->getAll($request);

      return response()->json($documentTypeArray);
   }

   public function show(Request $request, $id)
   {
      $documentType = $this->documentTypeRepository->getById($id);

      return response()->json($documentType);
   }

   // for Category here
   public function store(Request $request)
   {
      $documentType = null;

      if (is_null($request->id)) {
         $request->validate(
            [
               'name' => [
                  'required',
                  'min:2',
                  Rule::unique('document_types', 'name->' . locale())
                     ->where('document_category_id', $request->document_category_id)
               ],
               'document_category_id' => 'required'
            ],
            ['document_category_id.required' => 'Category is required.']
         );

         $documentType = $this->documentTypeRepository->create($request->all());
      } else {
         $request->validate(
            [
               'name' => 'required|min:2',
               'document_category_id' => 'required'
            ],
            ['document_category_id.required' => 'Category is required.']
         );

         $documentType = $this->documentTypeRepository->update($request->id, $request->all());
      }

      $documentType->responseStatus = true;

      return response()->json($documentType);
   }

   public function destroy(Request $request)
   {
      $documentType = $this->documentTypeRepository->getById($request->id);

      // if ($documentType->documentType_clients->count() > 0) {
      //    throw ValidationException::withMessages(['documentType' =>  strtoupper(
      //       $documentType->name
      //    ) . ' is used and cannot be deleted.']);
      // }

      if ($documentType->documentType_engagements > 0) {
         throw ValidationException::withMessages(['documentType' =>  strtoupper(
            $documentType->name
         ) . ' is used and cannot be deleted.']);
      }

      // if ($documentType->documentType_platforms->count() > 0) {
      //    throw ValidationException::withMessages(['documentType' =>  strtoupper(
      //       $documentType->name
      //    ) . ' is used and cannot be deleted.']);
      // }

      $documentType->delete();

      // Storage::disk('s3')->deleteDirectory(config('access.s3_path.documentTypes') . '/' . $documentType->id);

      return response()->json(true);
   }

   public function getCategories(Request $request)
   {
      $lang = \Lang::getLocale();

      $categories = Category::select('id', 'category_name')->get();

      $categoriesFinal = $categories->map(function ($categories) use ($lang) {
         // dd($categories->category_name);
         $categories->category_name = getTranslation($categories->category_name, $lang);
         return $categories;
      });

      return response()->json($categoriesFinal);
   }

   public function storeCategories(Request $request)
   {
      // dd($request->all());
      $lang = \Lang::getLocale();

      $request->validate(
         [
            "category_ids"  => "required",
            "id"      => "required|exists:documentTypes,id",
         ],
         [
            'category_ids.required'  => 'Category Name is required.',
            'id.required'           => 'DocumentTypes is required.',
            'id.exists'             => 'DocumentTypes doesn\'t exists.',
         ]
      );
      $categoryNames = array();
      foreach (json_decode($request->category_ids) as $category_id) {
         $categoryDocumentType = CategoryDocumentType::updateOrCreate(
            [
               "documentType_id"     => $request->id,
               "category_id"  => $category_id,
            ],
            [
               "documentType_id"     => $request->id,
               "category_id"  => $category_id,
            ]
         );

         array_push($categoryNames, getTranslation($categoryDocumentType->category->category_name, $lang));
      }

      return $categoryNames;
   }

   public function getNameDocumentType( $id, $lang ) {
        
      $document_type = DocumentType::whereId( $id ) -> first();

      $name = ucwords( string_to_value( $lang, $document_type -> name) );

      return $name;

  }

  public function getNameDocumentCategory( $id, $lang ) 
  {
     $name = '';
      if( ! empty( $id ) ) {
         $document_category = DocumentCategory::whereId( $id ) -> first();

         $name = ucwords( string_to_value( $lang, $document_category -> name) );
      }
      return $name;
      

   }

}
