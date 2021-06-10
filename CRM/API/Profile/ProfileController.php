<?php

namespace CRM\API\Profile;

use App;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\BrandClient;
use App\Models\Brand;
use App\Models\ClientEngagement;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Helpers\General\UploadHelper;
use Auth;

class ProfileController extends Controller
{
   private $profileRepository;

   public function __construct(ProfileRepository $profileRepository)
   {
      $this->profileRepository = $profileRepository;
   }


   public function index(Request $request)
   {
      $profiles = $this->profileRepository->getAll($request);

      return response()->json($profiles);
   }

   public function migrate_customers(){
      $profiles = $this->profileRepository->migrateCustomers();

      return response()->json($profiles);
   }

   public function load_profiles_under_brand(Request $request)
   {
      $profiles = $this->profileRepository->getByBrand($request);

      return response()->json($profiles);
   }

   public function load_filtered_profiles(Request $request)
   {
      $profiles = $this->profileRepository->filterProfiles($request);

      return response()->json($profiles);
   }

   
   public function show($id)
   {
      if (isset(request()->locale)) {
         App::setLocale(request()->locale);
      }

      $profile = $this->profileRepository->getById($id);

      return response()->json($profile);
   }

   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function find_match(Request $request)
   {
      $match = [];
      $matches = [];
      $user = Auth::user();
      if ($request->filter == "names") {
         $match = Profile::where("firstname", $request->firstname)->where('surname', $request->surname)->get();
      } elseif ($request->filter == "email") {
         $match = Profile::where("primary_email", $request->email)->get();
      }

      //Get only same organization matches
      foreach($match as $key => $profile){
         foreach($profile->brands as $brand){
            if(in_array($brand->organization, $user->organization_ids)){
               $matches[] = $profile;
            }
         }
      }


      if (count($matches) == 0) {
         $matches = [];
      }

      return response()->json($matches);
   }

   // for Add client engagements here
   public function add_client_engagements(Request $request)
   {
      $request->validate([
         'profile_id' => 'required|integer',
         'client_engagements.*.engagement_id' => 'required|integer',
         'client_engagements.*.engagement_date' => 'required|string',
      ], [
         'client_engagements.*.engagement_id.required' => 'The engagement field is required',
         'client_engagements.*.engagement_date.required' => 'The engagement date field is required',
      ]);

      $client_engagements = $request->client_engagements;

      foreach ($client_engagements as $client_engagement) {
         $this->profileRepository->add_client_engagement($request->profile_id, $client_engagement);
      }

      $profile = Profile::findOrfail($request->profile_id);
      
      $response = [
         "client_engagements" => $profile->client_engagements,
         "responseStatus" => true
      ];

      return response()->json($response);
   }

   // for Add contacts here
   public function add_contacts(Request $request)
   {
      $request->validate([
         'profile_id' => 'required|integer',
         'contacts.*.contact_info' => 'required|string',
         'contacts.*.contact_type_id' => 'required|integer',
      ], [
         'contacts.*.contact_info.required' => 'The contact details field is required',
         'contacts.*.contact_type_id.required' => 'The contact type field is required',
      ]);

      $contacts = $request->contacts;
      foreach ($contacts as $contact) {
         $this->profileRepository->add_contact($request->profile_id, $contact);
      }

      $profile = Profile::findOrfail($request->profile_id);
      
      $response = [
         "contacts" => $profile->contacts,
         "responseStatus" => true
      ];

      return response()->json($response);
   }

   // for Add attachments here
   public function add_attachments(Request $request)
   {
      $attachments = json_decode($request->attachments_info);
      foreach ($attachments as $index => $attachment) {
         $this->profileRepository->add_attachment($request->profile_id, $index, $attachment);
      }

      $profile = Profile::findOrfail($request->profile_id);
      
      $response = [
         "attachments" => $profile->attachments,
         "responseStatus" => true
      ];

      return response()->json($response);
   }

   // for Add notes here
   public function add_notes(Request $request)
   {
      $request->validate([
         'profile_id' => 'required|integer',
         'notes.*.date_requested' => 'required|string',
         'notes.*.notes' => 'required|string',
      ],[
         'notes.*.notes.required' => 'Notes is required.'
      ]);
      // dd($request->notes);
      $notes = $request->notes;
      $profile = $this->profileRepository->add_notes($request->profile_id, $notes);

      $profile = Profile::findOrfail($request->profile_id);
      
      $response = [
         "notes" => $profile->notes,
         "responseStatus" => true
      ];

      return response()->json($response);
   }


   // for Add client engagements here
   public function link_to_brands(Request $request)
   {
      $request->validate([
         'profile_id' => 'required|integer',
      ]);

      $profile = Profile::findOrfail($request->profile_id);
      $requested_brands = $request->brands;

      $current_brands = $profile->brand_ids;

      if ($requested_brands == null) {
         $requested_brands = [];
      }

      //Add requested
      foreach ($requested_brands as $brand_id) {
         if (!in_array($brand_id, $current_brands)) {
            $new_brand = BrandClient::create([
               "profile_id" => $profile->id,
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

      $profile->refresh();
      $profile->responseStatus = true;

      return response()->json($profile);
   }

   // for Add client engagements here
   public function update_note(Request $request)
   {
      $notes = $this->profileRepository->update_note($request->id, $request->note);
      
      $response = [
         "notes" => $notes,
         "responseStatus" => true
      ];

      return response()->json($response);
   }

   // for Add client engagements here
   public function update_contact(Request $request)
   {
      $request->validate([
         'id' => 'required|integer',
         'contacts.*.contact_info' => 'required|string',
         'contacts.*.contact_type_id' => 'required|integer',
      ], [
         'contacts.*.contact_info.required' => 'The contact details field is required',
         'contacts.*.contact_type_id.required' => 'The contact type field is required',
      ]);

      $contacts = $this->profileRepository->update_contact($request->id, $request->contact);

      $response = [
         "contacts" => $contacts,
         "responseStatus" => true
      ];

      return response()->json($response);
   }

   // for Add client engagements here
   public function update_attachment(Request $request)
   {
      $request->validate([
         'attachment' => 'required']);

      $data = json_decode($request->attachments_info);

      $attachments = $this->profileRepository->update_attachment($request->id, $data);

      $response = [
         "attachments" => $attachments,
         "responseStatus" => true
      ];

      return response()->json($response);
   }

   // for Add client engagements here
   public function update_client_engagement(Request $request)
   {
      $request->validate([
         'id' => 'required|integer',
         'client_engagement.engagement_id' => 'required|integer',
         'client_engagement.engagement_date' => 'required|string',
      ], [
         'client_engagements.*.engagement_id.required' => 'The engagement field is required',
         'client_engagements.*.engagement_date.required' => 'The engagement date field is required',
      ]);

      $client_engagements = $this->profileRepository->update_client_engagement($request->id, $request->client_engagement);

      $response = [
         "client_engagements" => $client_engagements,
         "responseStatus" => true
      ];

      return response()->json($response);
   }


   // for Add client engagements here
   public function delete_note(Request $request)
   {
      $notes = $this->profileRepository->delete_note($request->id, $request->note);

      $response = [
         "notes" => $notes,
         "responseStatus" => true
      ];

      return response()->json($response);
   }

   // for Add client engagements here
   public function delete_contact(Request $request)
   {
      $contacts = $this->profileRepository->delete_contact($request->id);

      $response = [
         "contacts" => $contacts,
         "responseStatus" => true
      ];

      return response()->json($response);
   }
   
   // for Add client engagements here
   public function delete_attachment(Request $request)
   {
      $attachments = $this->profileRepository->delete_attachment($request->attachment_info);
      
      $response = [
         "attachments" => $attachments,
         "responseStatus" => true
      ];

      return response()->json($response);
   }

   // for Add client engagements here
   public function delete_client_engagement(Request $request)
   {
      $client_engagements = $this->profileRepository->delete_client_engagement($request->id);

      $response = [
         "client_engagements" => $client_engagements,
         "responseStatus" => true
      ];

      return response()->json($response);
   }

   // for Category here
   public function store(Request $request)
   {
      $user = Auth::user();

      $request->validate([
         'firstname' => 'required|min:1',
         'surname' => 'required|min:1',
         'client_engagements.*.engagement_id' => 'required|integer',
         'client_engagements.*.engagement_date' => 'required|string',
         'contacts.*.contact_info' => 'required|string',
         'contacts.*.contact_type_id' => 'required|integer',
         'jobs.*.job_category_id' => 'required|integer',
      ], [
         'client_engagements.*.engagement_id.required' => 'The engagement field is required',
         'client_engagements.*.engagement_date.required' => 'The engagement date field is required',
         'contacts.*.contact_info.required' => 'The contact details field is required',
         'contacts.*.contact_type_id.required' => 'The contact type field is required',
         'jobs.*.job_category_id.required' => 'The job category field is required',
      ]);

      $profile = null;

      //Validate Client Engagements
      $request["client_engagements"] = json_decode($request["client_engagements"], true);
      
      foreach($request->client_engagements as $index => $client_engagement){
         if ($client_engagement["engagement_id"] == null) {
            throw ValidationException::withMessages(['client_engagements.' . $index  . '.engagement_id' => 'Please select an engagement type']);
         }

         if($client_engagement["engagement_date"] == "") {
            throw ValidationException::withMessages(['client_engagements.' . $index  . '.engagement_date' => 'Please enter the engagement date']);
         }
      }

      //Validate Client Engagements
      $request["jobs"] = json_decode($request["jobs"], true);
      
      foreach($request->jobs as $index => $job){
         if ($job["job_category_id"] == null) {
            throw ValidationException::withMessages(['jobs.' . $index  . '.job_category_id' => 'Please select a job type']);
         }
      }
      
      //Validate Contacts
      $request["contacts"] = json_decode($request["contacts"], true);
      
      foreach($request->contacts as $index => $contact){
         if ($contact["contact_type_id"] == null) {
            throw ValidationException::withMessages(['contacts.' . $index  . '.contact_type_id' => 'Please select a contact type']);
         }

         if($contact["contact_info"] == "") {
            throw ValidationException::withMessages(['contacts.' . $index  . '.contact_info' => 'Please fill-in the contact details']);
         }
      }
      
      if (is_null($request->id)) {
         if ($request->primary_email != '') {
            $matches = Profile::where('primary_email', $request->primary_email)->get();
            
            //Get only same organization matches
            foreach($matches as $key => $profile){
               foreach($profile->brands as $brand){
                  if($brand->organization != null){
                     if(in_array($brand->organization, $user->organization_ids)){
                        throw ValidationException::withMessages(['primary_email' => 'Profile with same primary email already exists']);
                     }
                  }
               }
            }
         }

         $profile = $this->profileRepository->create($request->all());
      } else {
         $profile = $this->profileRepository->update($request->id, $request->all());
      }
      
      $profile->refresh();
      $profile->responseStatus = true;
      return response()->json($profile);
   }

   public function destroy(Request $request)
   {
      $profile = $this->profileRepository->getById($request->id);

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

      foreach ($profile->client_engagements as $client_engagement) {
         $client_engagement->delete();
      }

      foreach ($profile->attachments as $attachment) {
         $this->deleteAttachment($attachment);
      }

      // remove from parent table before deleting
      $this->profileRepository->delete_customer($request->id);
      
      $profile->delete();

      return response()->json(true);
   }


   public function deleteAttachment($attachment)
   {
      $file_upload = new UploadHelper([
         'driver' => 's3',
         'id' => $attachment->id,
         's3_storage_path' => 'customer-care-tool',
         's3_folder_path' => config('access.s3_path.attachments'),
         'file' => '',
      ]);

      $file_upload->deleteDirectory();

      $attachment->delete();
   }
}
