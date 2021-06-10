<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {

        return view('backend.category.index');

    }


    public function getList( Request $request ) {
        
        $lang = $request -> input( 'lang' );

        $language = 'English';

        if( $lang == 'it') { $language = 'Italian'; } if( $lang == 'de' ) { $language = 'German'; }
        
        $get_categories = Category::orderBy( 'id', 'DESC' ) -> get();
        
        $categories = array();

        foreach( $get_categories as $datas ) {

            $name = $this -> getName( $datas[ 'id' ], $lang );
            
            $base_name = ( ! empty( $name ) ? $name : $this -> getName( $datas[ 'id' ], 'en' ) );

            if( $name == '' && $base_name == '' ) {

                $base_name_italy = $this -> getName( $datas[ 'id' ], 'it' );

                if( $base_name_italy !== '' ) {

                    $base_name = $base_name_italy;

                } else {

                    $base_name = $this -> getName( $datas[ 'id' ], 'de' );

                }
                
            }
            
            array_push( $categories, [

                'id' => $datas[ 'id' ],

                'name' => $base_name,

                'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),

                'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),

                'convertion' => ( ! empty( $name ) ? false : true ),

                'language' => $language

            ] );

        }
        
        return response() -> json( $categories );

    }

    public function store( Request $request ) {

        $lang = $request -> input( 'lang' );

        $request->validate([
                "name"   => "required|unique:categories,category_name->en",
            ],
            [
                'name.required'     => 'Category Name is required.',
                'name.unique'       =>  $request->name.' is already taken.',
            ]
        );
        
        $categoryName = ucwords($request->name);

        $category = Category::create([
            "category_name"     => json_encode([
                                    $lang => $categoryName,
                                ]),
        ]);

        return response() -> json( alert_success( $categoryName ) );

    }

    public function update( Request $request, Category $category ) {
       
        $lang = $request -> input( 'lang' );

        $request->validate([
                "name"   => "required|unique:categories,category_name->en",
            ],
            [
                'name.required'     => 'Category Name is required.',
                'name.unique'       =>  $request->name.' is already taken.',
            ]
        );

        $categoryName = string_add_json( $lang, ucwords( $request -> name ), string_remove( $lang, $category -> category_name ) );
        
        $category->update([
            "category_name"  => $categoryName
        ]);

        return response() -> json( alert_update( $request->name ) );

    }

    public function destroy(Request $request, Category $category ) {

        $lang = $request -> input( 'lang' );
       
        $name = $category->category_name;

        $category->delete();

        $message = ucwords( string_to_value( $lang, $name ) );

        return response() -> json( alert_delete( $message ) );

    }

    public function getName( $id, $lang ) {
        
        $categories = Category::whereId( $id ) -> first();

        $name = ucwords( string_to_value( $lang, $categories -> category_name) );

        return $name;

    }

    public function getCategoryName( Request $request, $id, $lang ) {

        $categories = Category::whereId( $id ) -> first();

        $message = ucwords( string_to_value( $lang, $categories -> category_name) );

        return response() -> json( [ 'name' => $message ] );

    }
}
