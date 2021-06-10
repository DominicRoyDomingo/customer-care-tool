<?php

namespace App\Http\Controllers\Backend;

use App\Models\Brand;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Backend\BrandRepository;

class BrandController extends Controller
{
    /**
     * @var BrandRepository
     */
    protected $brandRepository;

    /**
     * BrandController constructor.
     *
     * @param BrandRepository $brandRepository
     */
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = $this->brandRepository->allExtended();

        return view('backend.brands', compact(["brands"]));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $brand = $this->brandRepository->create($request->all());
        $brand = $brand->extendedInformation($brand);
        return response()->json($brand);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $brand = Brand::findOrfail($id);
        $brand = $brand->extendedInformation($brand);
        return response()->json($brand);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show_all()
    {
        $brands = $this->brandRepository->allExtended();

        return response()->json($brands);
    }

    public function swap($id) {
        $brand = Brand::findOrfail($id);
        
        session()->put('brand', $brand);
        
        return redirect()->back();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $brand = Brand::findOrfail($id);
        $brand = $this->brandRepository->update($brand, $request->all());
        
        $brand = $brand->extendedInformation($brand);
        return response()->json($brand);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = Brand::findOrfail($id);
        $brand->delete();
        return $brand;
    }

    
}
