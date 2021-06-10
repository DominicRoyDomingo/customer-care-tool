<?php

namespace App\Http\Controllers\Backend;

use App\Models\ContactType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Backend\ContactTypeRepository;

class ContactTypeController extends Controller
{
    /**
     * @var ContactTypeRepository
     */
    protected $contactTypeRepository;

    /**
     * ContactTypeController constructor.
     *
     * @param ContactTypeRepository $contactTypeRepository
     */
    public function __construct(ContactTypeRepository $contactTypeRepository)
    {
        $this->contactTypeRepository = $contactTypeRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contact_types = $this->contactTypeRepository->all();

        return view('backend.contact_types', compact(["contact_types"]));
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
        $contactType = $this->contactTypeRepository->create($request->all());
        return response()->json($contactType);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ContactType  $contactType
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contactType = ContactType::findOrfail($id);
        return response()->json($contactType);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show_all()
    {
        $contact_types = $this->contactTypeRepository->allExtended();

        return response()->json($contact_types);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ContactType  $contactType
     * @return \Illuminate\Http\Response
     */
    public function edit(ContactType $contactType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ContactType  $contactType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $contactType = ContactType::findOrfail($id);
        $contactType = $this->contactTypeRepository->update($contactType, $request->all());
        
        return response()->json($contactType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ContactType  $contactType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contactType = ContactType::findOrfail($id);
        $contactType->delete();
        return $contactType;
    }
}
