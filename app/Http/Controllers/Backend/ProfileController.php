<?php

namespace App\Http\Controllers\Backend;

use App\Models\Profile;
use App\Models\Brand;
use App\Models\Contact;
use App\Models\BrandClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Backend\ProfileRepository;
use App\Repositories\Backend\ContactTypeRepository;
use App\Repositories\Backend\BrandRepository;
use Auth;
use Khsing\World\World;
use Khsing\World\Models\Continent;
use Khsing\World\Models\Countries;


class ProfileController extends Controller
{
    /**
     * @var ProfileRepository
     */
    protected $profileRepository;

    /**
     * @var ContactTypeRepository
     */
    protected $contactTypeRepository;

    /**
     * @var BrandRepository
     */
    protected $brandRepository;

    /**
     * ProfileController constructor.
     *
     * @param ProfileRepository $profileRepository
     */
    public function __construct(ProfileRepository $profileRepository, ContactTypeRepository $contactTypeRepository, BrandRepository $brandRepository)
    {
        $this->profileRepository = $profileRepository;
        $this->contactTypeRepository = $contactTypeRepository;
        $this->brandRepository = $brandRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $countries = World::Countries();

        $brands = Brand::all();
        $filter =  request()->get('brand');
        $loaded_brand = null;
        $profiles = null;
        if ($filter != "none") {
            $loaded_brand = Brand::whereRaw('LOWER(`name`) LIKE ? ', [trim(strtolower($filter)) . '%'])->first();
            $profiles = $this->profileRepository->filterByBrand($loaded_brand->id);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $profiles = $this->profileRepository->filterByBrand($filter);
        }
        // dd($filter);
        //$profiles = $this->profileRepository->allExtended();

        $contact_types = $this->contactTypeRepository->allExtended();

        return view('backend.profiles', compact(["profiles", "contact_types", "brands", "loaded_brand", "countries"]));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function find_match(Request $request)
    {
        if ($request->filter == "names") {
            $match = Profile::where("firstname", $request->firstname)->where('surname', $request->surname)->get();
        } elseif ($request->filter == "email") {
            $match = Profile::where("primary_email", $request->email)->get();
        }


        if (count($match) > 0) {
            foreach ($match as $profile) {
                $profile = $profile->extendedInformation($profile);
            }
        }

        return response()->json($match);
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
        $profile = $this->profileRepository->create($request->all());
        $profile = $profile->extendedInformation($profile);
        return response()->json($profile);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $profile = Profile::findOrfail($id);
        $profile = $profile->extendedInformation($profile);
        return response()->json($profile);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function get_notes($id)
    {
        $profile = Profile::findOrfail($id);
        $profile = $profile->extendedInformation($profile);
        $notes = $profile->notes;
        $sorted_notes = collect($notes)->sortByDesc('date_added_order')->values();
        //$sorted_notes = collect($notes)->sortByDesc('date_requested_order')->values();

        return response()->json($sorted_notes);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function add_notes(Request $request, $id)
    {
        $profile = Profile::findOrfail($id);
        $notes = json_decode($profile->notes);
        $new_notes = $request->notes;
        foreach ($new_notes as $new_note) {
            $new_note["date_added"] = date("Y-m-d H:i:s");
            $new_note["created_by"] = Auth::user()->id;
            $new_note["index"] = count($notes);
            $notes[] = $new_note;
        }
        $profile->notes = json_encode($notes);
        $profile->save();

        $profile = $profile->extendedInformation($profile);

        return response()->json($profile);
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update_note(Request $request, $id)
    {
        $profile = Profile::findOrfail($id);
        $new_note = $request->all();
        $notes = json_decode($profile->notes);
        $notes[$new_note["index"]] = $new_note;
        $profile->notes = json_encode($notes);
        $profile->save();

        $profile = $profile->extendedInformation($profile);
        //$sorted_notes = collect($notes)->sortByDesc('date_requested_order')->values();

        return response()->json($profile);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function get_contacts($id)
    {
        $profile = Profile::findOrfail($id);
        $profile = $profile->extendedInformation($profile);

        return response()->json($profile->contacts);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function add_contacts(Request $request, $id)
    {
        $profile = Profile::findOrfail($id);
        $new_contacts = $request->contacts;
        foreach ($new_contacts as $new_contact) {
            $contact = Contact::create([
                'contact_type_id' => $new_contact["contact_type_id"],
                'contact_info' => $new_contact["contact_info"],
                'created_by' => Auth::user()->id,
                'profile_id' => $profile->id,
            ]);
        }
        $profile->save();

        $profile = $profile->extendedInformation($profile);

        return response()->json($profile);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $countries = World::Countries();

        $brands = Brand::all();
        $filter =  request()->get('brand');
        $loaded_brand = null;
        $profiles = null;
        if ($filter != "none") {
            $loaded_brand = Brand::whereRaw('LOWER(`name`) LIKE ? ', [trim(strtolower($filter)) . '%'])->first();
            $profiles = $this->profileRepository->filterByBrand($loaded_brand->id);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $profiles = $this->profileRepository->filterByBrand($filter);
        }

        //$profiles = $this->profileRepository->allExtended();

        $contact_types = $this->contactTypeRepository->allExtended();

        $profile = Profile::findOrfail($id);
        $profile = $profile->extendedInformation($profile);
        //dd($profile);
        return view('backend.view_profile', compact(["profile", "contact_types", "brands", "loaded_brand", "countries"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function link_to_brand(Request $request, $id)
    {
        $profile = Profile::findOrfail($id);
        $profile = $profile->extendedInformation($profile);
        $current_brands = $profile->brand_ids;
        $requested_brands = $request["brands"];
        if ($requested_brands == null) {
            $requested_brands = [];
        }

        //Add requested
        foreach ($requested_brands as $brand_id) {
            if (!in_array($brand_id, $current_brands)) {
                $new_brand = BrandClient::create([
                    "profile_id" => $id,
                    "brand_id" => $brand_id
                ]);
            }
        }

        //Remove non-surviving brands
        foreach ($profile->profile_brands as $client_brand) {
            if (!in_array($client_brand->brand_id, $requested_brands)) {
                $client_brand->delete();
            }
        }

        //
        $profile = $profile->refresh();
        $profile = $profile->extendedInformation($profile);

        return response()->json($profile);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function edit(Profile $profile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $profile = Profile::findOrfail($id);
        $profile = $this->profileRepository->update($profile, $request->all());
        $profile->refresh();
        $profile = $profile->extendedInformation($profile);
        return response()->json($profile);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $profile = Profile::findOrfail($id);

        foreach ($profile->contacts as $contact) {
            $contact->delete();
        }

        foreach ($profile->profile_brands as $profile_brand) {
            $profile_brand->delete();
        }

        if (isset($profile->client_location)) {
            $profile->client_location->delete();
        }

        foreach ($profile->jobs as $job) {
            $job->delete();
        }

        $profile->delete();
        return $profile;
    }

    /**
     * country_location_statistics
     *
     * @return void
     */
    public function country_location_statistics()
    {
        $type = 'country';
        $filter = request()->get('brand');
        $loaded_brand = null;
        $profiles = null;
        if ($filter > 0) {
            $loaded_brand = Brand::whereRaw('id = ' . $filter)->first();
            $profiles = $this->profileRepository->getStatsByLocation($type, $loaded_brand->id);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $profiles = $this->profileRepository->getStatsByLocation($type, $filter);
        }

        return $profiles;
    }

    /**
     * province_location_statistics
     *
     * @return void
     */
    public function province_location_statistics()
    {
        $type = 'province';
        $filter = request()->get('brand');
        $loaded_brand = null;
        $profiles = null;
        if ($filter > 0) {
            $loaded_brand = Brand::whereRaw('id = ' . $filter)->first();
            $profiles = $this->profileRepository->getStatsByLocation($type, $loaded_brand->id);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $profiles = $this->profileRepository->getStatsByLocation($type, $filter);
        }

        return $profiles;
    }

    /**
     * city_location_statistics
     *
     * @return void
     */
    public function city_location_statistics()
    {
        $type = 'city';
        $filter = request()->get('brand');
        $loaded_brand = null;
        $profiles = null;
        if ($filter > 0) {
            $loaded_brand = Brand::whereRaw('id = ' . $filter)->first();
            $profiles = $this->profileRepository->getStatsByLocation($type, $loaded_brand->id);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $profiles = $this->profileRepository->getStatsByLocation($type, $filter);
        }

        return $profiles;
    }

    /**
     * category_job_statistics
     *
     * @return void
     */
    public function category_job_statistics()
    {
        $type = 'category';
        $filter = request()->get('brand');
        $lang = request()->get('lang');
        $loaded_brand = null;
        $profiles = null;
        if ($filter > 0) {
            $loaded_brand = Brand::whereRaw('id = ' . $filter)->first();
            $profiles = $this->profileRepository->getStatsByJob($type, $loaded_brand->id, $lang);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $profiles = $this->profileRepository->getStatsByJob($type, $filter, $lang);
        }

        return $profiles;
    }

    /**
     * profession_job_statistics
     *
     * @return void
     */
    public function profession_job_statistics()
    {
        $type = 'profession';
        $filter = request()->get('brand');
        $lang = request()->get('lang');
        $loaded_brand = null;
        $profiles = null;
        if ($filter > 0) {
            $loaded_brand = Brand::whereRaw('id = ' . $filter)->first();
            $profiles = $this->profileRepository->getStatsByJob($type, $loaded_brand->id, $lang);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $profiles = $this->profileRepository->getStatsByJob($type, $filter, $lang);
        }

        return $profiles;
    }

    /**
     * specialization_job_statistics
     *
     * @return void
     */
    public function specialization_job_statistics()
    {
        $type = 'specialization';
        $filter = request()->get('brand');
        $lang = request()->get('lang');
        $loaded_brand = null;
        $profiles = null;
        if ($filter > 0) {
            $loaded_brand = Brand::whereRaw('id = ' . $filter)->first();
            $profiles = $this->profileRepository->getStatsByJob($type, $loaded_brand->id, $lang);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $profiles = $this->profileRepository->getStatsByJob($type, $filter, $lang);
        }

        return $profiles;
    }

    /**
     * tabular_location_statistics
     *
     * @return void
     */
    public function tabular_location_statistics()
    {
        $brand = request()->get('brand');
        $loaded_brand = null;
        $statistics = null;
        if ($brand > 0) {
            $loaded_brand = Brand::whereRaw('id = ' . $brand)->first();
            $statistics = $this->profileRepository->getTabularLocationStats($loaded_brand->id);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $statistics = $this->profileRepository->getTabularLocationStats($brand);
        }

        return $statistics;
    }

    /**
     * tabular_job_statistics
     *
     * @return void
     */
    public function tabular_job_statistics()
    {
        $brand = request()->get('brand');
        $lang = request()->get('lang');
        $loaded_brand = null;
        $statistics = null;
        if ($brand > 0) {
            $loaded_brand = Brand::whereRaw('id = ' . $brand)->first();
            $statistics = $this->profileRepository->getTabularJobStats($loaded_brand->id, $lang);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $statistics = $this->profileRepository->getTabularJobStats($brand, $lang);
        }

        return $statistics;
    }

    /**
     * detailed_job_stats
     *
     * @return void
     */
    public function detailed_job_stats()
    {
        $brand = request()->get('brand');
        $lang = request()->get('lang');

        $country = request()->get('country');
        $province = request()->get('province');
        $city = request()->get('city');

        $loaded_brand = null;
        $statistics = null;
        if ($brand > 0) {
            $loaded_brand = Brand::whereRaw('id = ' . $brand)->first();
            $statistics = $this->profileRepository->getDetailedJobStats($loaded_brand->id, $lang, $country, $province, $city);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $statistics = $this->profileRepository->getDetailedJobStats($brand, $lang, $country, $province, $city);
        }

        return $statistics;
    }

    /**
     * detailed_location_stats
     *
     * @return void
     */
    public function detailed_location_stats()
    {
        $brand = request()->get('brand');
        $lang = request()->get('lang');

        $category = request()->get('category');
        $profession = request()->get('profession');
        $specialization = request()->get('specialization');

        $loaded_brand = null;
        $statistics = null;
        if ($brand > 0) {
            $loaded_brand = Brand::whereRaw('id = ' . $brand)->first();
            $statistics = $this->profileRepository->getDetailedLocStats($loaded_brand->id, $lang, $category, $profession, $specialization);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $statistics = $this->profileRepository->getDetailedLocStats($brand, $lang, $category, $profession, $specialization);
        }

        return $statistics;
    }

    /**
     * export_job_stats
     *
     * @return void
     */
    public function export_job_stats()
    {
        $brand = request()->get('brand');
        $lang = request()->get('lang');

        $country = request()->get('country');
        $province = request()->get('province');
        $city = request()->get('city');
        $search = request()->get('search');

        $loaded_brand = null;
        $statistics = null;
        if ($brand > 0) {
            $loaded_brand = Brand::whereRaw('id = ' . $brand)->first();
            $statistics = $this->profileRepository->exportJobStats($loaded_brand->id, $lang, $country, $province, $city, $search);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $statistics = $this->profileRepository->exportJobStats($brand, $lang, $country, $province, $city, $search);
        }

        return $statistics;
    }

    /**
     * export_location_stats
     *
     * @return void
     */
    public function export_location_stats()
    {
        $brand = request()->get('brand');
        $lang = request()->get('lang');

        $category = request()->get('category');
        $profession = request()->get('profession');
        $specialization = request()->get('specialization');
        $search = request()->get('search');

        $loaded_brand = null;
        $statistics = null;
        if ($brand > 0) {
            $loaded_brand = Brand::whereRaw('id = ' . $brand)->first();
            $statistics = $this->profileRepository->exportLocationStats($loaded_brand->id, $lang, $category, $profession, $specialization, $search);
        } else {
            $loaded_brand = new \stdClass();
            $loaded_brand->id = "";
            $statistics = $this->profileRepository->exportLocationStats($brand, $lang, $category, $profession, $specialization, $search);
        }

        return $statistics;
    }
}
