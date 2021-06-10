<?php

namespace CRM\API\Profile;

use App\Exceptions\GeneralException;
use App\Helpers\General\UploadHelper;
use App\Models\Profile;
use App\Models\Attachment;
use App\Models\Brand;
use App\Models\BrandClient;
use App\Models\Jobs\ClientJob;
use App\Models\Contact;
use App\Models\ClientLocation;
use App\Models\ClientEngagement;
use App\Models\Customer;
use App\Models\ProviderProfile;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Auth;
use CRM\API\Customer\CustomerRepository;

class ProfileRepository extends BaseRepository
{
	var $customerRepository;

	public function __construct(Profile $model)
	{
		$this->model = $model;
	
		$this->customerRepository = new CustomerRepository(new Customer());
	}

	public function getAll($request)
	{
		$profiles = null;
		$brand = $request->brand;
		if ((int) $brand !== 0) {
			$profiles = $this->model->select('profiles.*')
				->when($request->brand !== 0, function ($query) use ($request) {
					$brand = $request->brand;
					$query
						->join('brand_clients', 'brand_clients.profile_id', '=', 'profiles.id')
						->where(function ($whereQuery) use ($brand) {

							if ($brand !== 'all') {
								$whereQuery
									->where('brand_clients.brand_id', $brand)
									->whereNotNull('profiles.firstname')
									->whereNotNull('profiles.surname');
							} else {
								$whereQuery
									->whereNotNull('profiles.firstname')
									->whereNotNull('profiles.surname'); // Primary Email is commonly empty
							}
						});
				})
				->get();
		} else {
			if ((int) $brand === 0) {
				$profiles = $this->model
					->select('profiles.*')
					->whereDoesntHave('profile_brands')->get();
			} else {
				$profiles = $this->model
					->select('profiles.*')
					->get();
			}
		}

		return $profiles;
	}

	public function migrateCustomers(){
		$profiles = Profile::all();
		$provider_profiles = ProviderProfile::all()->pluck('profile')->toArray();
		

		$migrated = [];

		foreach($profiles as $profile){
			if(!in_array($profile->id, $provider_profiles)){
				
				//This is where customers get added on profile on Migration
				$customer = $this->customerRepository->create([
					'profile_id' => $profile->id
				]);
				
				$customer->profile = $customer->profile;

				$migrated[] = $customer;
			}
		}

		return $migrated;
	}

	public function getByBrand($request){
		$profiles = null;
		$brand = $request->brand;
		//For local testing
		ini_set('max_execution_time', 36000);

		if ((int) $brand !== 0) {
			$profiles = $this->model->select('profiles.firstname', 'profiles.surname', 'profiles.id')
				->when($request->brand !== 0, function ($query) use ($request) {
					$brand = $request->brand;
					$query
						->join('brand_clients', 'brand_clients.profile_id', '=', 'profiles.id')
						->where(function ($whereQuery) use ($brand) {

							if ($brand !== 'all') {
								$whereQuery
									->where('brand_clients.brand_id', $brand)
									->whereNotNull('profiles.firstname')
									->whereNotNull('profiles.surname');
							} else {
								$whereQuery
									->whereNotNull('profiles.firstname')
									->whereNotNull('profiles.surname'); // Primary Email is commonly empty
							}
						});
				})
				->get();
		} else {
			if ((int) $brand === 0) {
				$profiles = $this->model
					->select('profiles.firstname', 'profiles.surname', 'profiles.id')
					->whereDoesntHave('profile_brands')
					->toSql();
			} else {
				$profiles = $this->model
					->select('profiles.firstname', 'profiles.surname', 'profiles.id')
					->get();
			}
		}
		
		/*
			$profiles->each->setAppends([
				'full_name', 'brand_names', 'location_display',
				'country_name', 'province_name', 'city_name',
				'job_category_name', 'job_profession_name',
				'job_description_name', 'job_display_name', 'is_loading'
			]);
		*/
				// dd($profiles);
		$profiles->each->setAppends([
			'full_name', 'brand_names',
			'is_loading'
		]);

		$profiles->makeHidden(['client_engagements','contacts','attachments', 'profile_brands']);
		//$profiles->load('profile_brands');
			
		return $profiles;
	}

	public function filterProfiles($request){
		$profiles = null;
		$brand = $request->brand;

		//For local testing
		ini_set('max_execution_time', 36000);

		if ((int) $brand !== 0) {
			$profiles = $this->model->select('profiles.firstname', 'profiles.surname', 'profiles.id')
				->when($request->filter == "Jobs", function ($query) use ($request) {
					$query
						->join('client_jobs', 'client_jobs.profile_id', '=', 'profiles.id')
						->join('job_category', 'client_jobs.job_category_id', '=', 'job_category.id')
						->join('job_profession', 'client_jobs.job_profession_id', '=', 'job_profession.id')
						->join('job_description', 'client_jobs.job_description_id', '=', 'job_description.id');
				})
				->when($request->filter == "Locations", function ($query) use ($request) {
					$query
						->join('client_locations', 'client_locations.profile_id', '=', 'profiles.id')
						->join('world_countries', 'client_locations.world_countries_id', '=', 'world_countries.id')
						->join('world_divisions', 'client_locations.world_provinces_id', '=', 'world_divisions.id')
						->join('world_cities', 'client_locations.world_cities_id', '=', 'world_cities.id');
				})
				->when($request->filter == "Engagements", function ($query) use ($request) {
					$query
						->join('client_engagements', 'client_engagements.profile_id', '=', 'profiles.id')
						->join('engagements', 'client_engagements.engagement_id', '=', 'engagements.id')
						->join('platform', 'client_engagements.platform_id', '=', 'platform.id')
						->join('platform_type', 'platform.platform_type', '=', 'platform_type.id');
				})
				->when($request->brand !== 0, function ($query) use ($request) {
					$query
						->join('brand_clients', 'brand_clients.profile_id', '=', 'profiles.id')
						->where(function ($whereQuery) use ($request) {
							$brand = $request->brand;
							$filter = $request->filter;

							if ($brand !== 'all') {
								$whereQuery
									->where('brand_clients.brand_id', $brand)
									->whereNotNull('profiles.firstname')
									->whereNotNull('profiles.surname');
							} else {
								$whereQuery
									->whereNotNull('profiles.firstname')
									->whereNotNull('profiles.surname'); // Primary Email is commonly empty
							}
						});
				})
				->where(function($query) use ($request)  {
					$filter = $request->filter;
					$filter_value = '%' .  $request->filter_value . '%';
					
					if($request->filter == "Name") {
						$query
							->whereRaw(
								'concat_ws(" ", `profiles`.`firstname`, `profiles`.`surname`) LIKE \'' . $filter_value . '\'' .
								' or concat_ws(", ", `profiles`.`surname`, `profiles`.`firstname`) LIKE \'' . $filter_value . '\''
							)
							->orWhere('profiles.firstname', "LIKE", $filter_value)
							->orWhere('profiles.surname', "LIKE", $filter_value);
					}
				 })
				 ->where(function($query) use ($request)  {
					 if($request->filter == "Jobs") {
						$filter = $request->filter;
						$filter_value = '%' .  $request->filter_value . '%';

						$query
							->where('job_category.category', "LIKE", $filter_value)
							->orWhere('job_profession.profession', "LIKE", $filter_value)
							->orWhere('job_description.description', "LIKE", $filter_value);
					 }
				  })
				  ->where(function($query) use ($request)  {
					  if($request->filter == "Locations") {
						 $filter = $request->filter;
						 $filter_value = '%' .  $request->filter_value . '%';
 
						 $query
							 ->where('world_countries.name', "LIKE", $filter_value)
							 ->orWhere('world_divisions.name', "LIKE", $filter_value)
							 ->orWhere('world_cities.name', "LIKE", $filter_value);
					  }
				   })
				   ->where(function($query) use ($request)  {
					   if($request->filter == "Engagements") {
						  $filter = $request->filter;
						  $filter_value = '%' .  $request->filter_value . '%';
  
						  $query
							  ->where('engagements.engagement', "LIKE", $filter_value)
							  ->orWhere('platform_type.name', "LIKE", $filter_value);
					   }
					})
					->groupBy('profiles.id')
				->get();
				//dd($profiles);

				//dd($query->toSql(), $query->getBindings());

		} else {
			if ((int) $brand === 0) {
				$profiles = $this->model
					->select('profiles.firstname', 'profiles.surname', 'profiles.id')
					->when($request->filter == "Jobs", function ($query) use ($request) {
						$query
							->join('client_jobs', 'client_jobs.profile_id', '=', 'profiles.id')
							->join('job_category', 'client_jobs.job_category_id', '=', 'job_category.id')
							->join('job_profession', 'client_jobs.job_profession_id', '=', 'job_profession.id')
							->join('job_description', 'client_jobs.job_description_id', '=', 'job_description.id');
					})
					->when($request->filter == "Locations", function ($query) use ($request) {
						$query
							->join('client_locations', 'client_locations.profile_id', '=', 'profiles.id')
							->join('world_countries', 'client_locations.world_countries_id', '=', 'world_countries.id')
							->join('world_divisions', 'client_locations.world_provinces_id', '=', 'world_divisions.id')
							->join('world_cities', 'client_locations.world_cities_id', '=', 'world_cities.id');
					})
					->when($request->filter == "Engagements", function ($query) use ($request) {
						$query
							->join('client_engagements', 'client_engagements.profile_id', '=', 'profiles.id')
							->join('engagements', 'client_engagements.engagement_id', '=', 'engagements.id')
							->join('platform', 'client_engagements.platform_id', '=', 'platform.id')
							->join('platform_type', 'platform.platform_type', '=', 'platform_type.id');
					})
					->whereDoesntHave('profile_brands')
					->where(function($query) use ($request)  {
						$brand = $request->brand;
						$filter = $request->filter;
						$filter_value = '%' .  $request->filter_value . '%';
						
						if($request->filter == "Name") {
							$query
								->whereRaw(
									'concat_ws(" ", `profiles`.`firstname`, `profiles`.`surname`) LIKE \'' . $filter_value . '\'' .
									' or concat_ws(", ", `profiles`.`surname`, `profiles`.`firstname`) LIKE \'' . $filter_value . '\''
								)
								->orWhere('profiles.firstname', "LIKE", $filter_value)
								->orWhere('profiles.surname', "LIKE", $filter_value);
						}
					 })
					  ->where(function($query) use ($request)  {
						  if($request->filter == "Jobs") {
							 $filter = $request->filter;
							 $filter_value = '%' .  $request->filter_value . '%';
	 
							 $query
								 ->where('job_category.category', "LIKE", $filter_value)
								 ->orWhere('job_profession.profession', "LIKE", $filter_value)
								 ->orWhere('job_description.description', "LIKE", $filter_value);
						  }
					   })
					   ->where(function($query) use ($request)  {
						   if($request->filter == "Locations") {
							  $filter = $request->filter;
							  $filter_value = '%' .  $request->filter_value . '%';
	  
							  $query
								  ->where('world_countries.name', "LIKE", $filter_value)
								  ->orWhere('world_divisions.name', "LIKE", $filter_value)
								  ->orWhere('world_cities.name', "LIKE", $filter_value);
						   }
						})
						->where(function($query) use ($request)  {
							if($request->filter == "Engagements") {
							   $filter = $request->filter;
							   $filter_value = '%' .  $request->filter_value . '%';
	   
							   $query
								   ->where('engagements.engagement', "LIKE", $filter_value)
								   ->orWhere('platform_type.name', "LIKE", $filter_value);
							}
						 })
						 ->groupBy('profiles.id')
					->get();
			} else {
				$profiles = $this->model
					->select('profiles.firstname', 'profiles.surname', 'profiles.id')
					->get();
			}
		}
		
		/*
		
		$profiles->each->setAppends([
			'full_name', 'brand_names', 'location_display',
			'country_name', 'province_name', 'city_name',
			'job_category_name', 'job_profession_name',
			'job_description_name', 'job_display_name', 'is_loading'
		]);
		
		*/

		$profiles->each->setAppends([
			'full_name', 'brand_names',
			'is_loading'
		]);
		
		$profiles->makeHidden(['client_engagements','contacts','attachments', 'profile_brands']);
		//$profiles->load('profile_brands');

		return $profiles;
	}

	/**
	 * @param array $data
	 *
	 * @throws \Exception
	 * @throws \Throwable
	 * @return User
	 */
	public function create(array $data): Profile
	{
		return DB::transaction(function () use ($data) {
			if (isset($data["notes"])) {
				$notes = json_decode($data["notes"]);
				foreach ($notes as $index => $note) {
					$note->date_added = date("Y-m-d H:i:s");
					$note->created_by = Auth::user()->id;
					$note->index = $index;

					$notes[$index] = $note;
					//dd($note);
				}
				$data["notes"] = json_encode($notes);
			} else {
				$data["notes"] = json_encode([]);
			}

			$profile = $this->model::create([
				'surname' => $data['surname'],
				'firstname' => $data['firstname'],
				'middlename' => $data['middlename'],
				'nickname' => $data['nickname'],
				'primary_email' => $data['primary_email'],
				'notes' => $data['notes'],
			]);


			//This is where customers get added on profile
			$customer = $this->customerRepository->create([
				'profile_id' => $profile->id
			]);

			//Add initial brand
			if (isset($data["brands"])) {
				$brands = json_decode($data["brands"]);
				foreach ($brands as $brand_id) {
					if (trim($brand_id) == "" || $brand_id == 0) {
						continue;
					}

					$brand = BrandClient::create([
						'brand_id' => $brand_id,
						'profile_id' => $profile->id,
					]);
				}
			}

			$this->put_contacts($profile, $data);

			$this->put_location($profile, $data);

			$this->put_jobs($profile, $data);

			$this->put_client_engagements($profile, $data);

			$this->put_attachments($profile, $data);
			
			
			if ($profile) {
				return $profile;
			}

			throw new GeneralException(__('exceptions.backend.access.profiles.create_error'));
		});
	}


	/**
	 * @param User  $user
	 * @param array $data
	 *
	 * @throws GeneralException
	 * @throws \Exception
	 * @throws \Throwable
	 * @return User
	 */
	public function update($id, array $data): Profile
	{

		return DB::transaction(function () use ($id, $data) {
			$profile = $this->getById($id);

			if (isset($data["notes"])) {
				$notes = json_decode($data["notes"]);
				foreach ($notes as $index => $note) {
					if (!isset($note->date_added)) {
						$note->date_added = date("Y-m-d H:i:s");
						$note->created_by = Auth::user()->id;
						$note->index = $index;
						$notes[$index] = $note;
					}
				}

				$data["notes"] = json_encode($notes);
			} else {
				$data["notes"] = json_encode([]);
			}

			$this->put_contacts($profile, $data);

			$this->put_location($profile, $data);

			$this->put_jobs($profile, $data);

			$this->put_client_engagements($profile, $data);

			$this->put_attachments($profile, $data);

			if($data['middlename'] == null || $data['middlename'] == 'null'){
				$data['middlename'] = '';
			}

			if($data['nickname'] == null || $data['nickname'] == 'null'){
				$data['nickname'] = '';
			}

			if($data['primary_email'] == null || $data['primary_email'] == 'null'){
				$data['primary_email'] = '';
			}

			if ($profile->update([
				'surname' => $data['surname'],
				'firstname' => $data['firstname'],
				'middlename' => $data['middlename'],
				'nickname' => $data['nickname'],
				'primary_email' => $data['primary_email'],
				'notes' => $data['notes'],
			])) {
				return $profile;
			}

			throw new GeneralException(__('exceptions.backend.access.profiles.update_error'));
		});
	}

	public function put_location($profile, $data)
	{
		if (isset($data["location"])) {
			$location = json_decode($data["location"], true);
			
			if (isset($profile->client_location)) {
				$client_location = ClientLocation::findOrfail($profile->client_location->id);
				$client_location->world_countries_id = $location['country_id'];
				$client_location->world_provinces_id = $location['province_id'];
				$client_location->world_cities_id = $location['city_id'];
				$client_location->save();
			} else {
				$client_location = ClientLocation::create([
					'profile_id' => $profile->id,
					'world_countries_id' => $location['country_id'],
					'world_provinces_id' => $location['province_id'],
					'world_cities_id' => $location['city_id'],
				]);
			}
		}
	}

	public function put_contacts($profile, $data)
	{
		$existing_contacts = $profile->contacts->pluck('id')->toArray();
		$kept_contacts = [];

		if (isset($data["contacts"])) {
			$contacts = $data["contacts"];
			foreach ($contacts as $contact_data) {
				if ($contact_data["id"] == "" || $contact_data["id"] == null) {
					$contact = Contact::create([
						'contact_type_id' => $contact_data['contact_type_id'],
						'contact_info' => $contact_data['contact_info'],
						'created_by' => Auth::user()->id,
						'profile_id' => $profile->id
					]);

					$kept_contacts[] = $contact->id;
				} else {
					$contact = Contact::findOrfail($contact_data["id"]);
					$contact->contact_type_id = $contact_data['contact_type_id'];
					$contact->contact_info = $contact_data['contact_info'];

					//dd($contact);
					$contact->save();
					$kept_contacts[] = $contact->id;
				}
			}
		}

		//Delete contact unincluded
		foreach ($existing_contacts as $delete_contact) {
			if (in_array($delete_contact, $kept_contacts))
				continue;

			$contact = Contact::findOrfail($delete_contact);
			$contact->delete();
		}
	}

	public function put_jobs($profile, $data)
	{
		$existing_jobs = $profile->jobs->pluck('id')->toArray();
		$kept_jobs = [];

		if (isset($data["jobs"])) {
			$jobs = $data["jobs"];
			foreach ($jobs as $job) {
				if ($job["id"] == "" || $job["id"] == null) {
					$new_job = ClientJob::create([
						'job_category_id' => $job['job_category_id'],
						'job_profession_id' => $job['job_profession_id'],
						'job_description_id' => $job['job_description_id'],
						'profile_id' => $profile->id,
					]);

					$kept_jobs[] = $new_job->id;
				} else {
					$old_job = ClientJob::findOrfail($job["id"]);
					$old_job->job_category_id = $job["job_category_id"];
					$old_job->job_profession_id = $job["job_profession_id"];
					$old_job->job_description_id = $job["job_description_id"];
					$old_job->save();

					$kept_jobs[] = $old_job->id;
				}
			}
		}
		//Delete contact unincluded
		foreach ($existing_jobs as $delete_job) {
			if (in_array($delete_job, $kept_jobs))
				continue;

			$job = ClientJob::findOrfail($delete_job);
			$job->delete();
		}
	}

	public function put_client_engagements($profile, $data)
	{
		$existing_client_engagements = $profile->client_engagements->pluck('id')->toArray();
		$kept_client_engagements = [];

		if (isset($data["client_engagements"])) {
			$client_engagements = $data["client_engagements"];
			foreach ($client_engagements as $client_engagement) {
				if (!isset($client_engagement["id"])) {
					$new_client_engagement = ClientEngagement::create([
						'profile_id' => $profile->id,
						'engagement_id' => $client_engagement["engagement_id"],
						'engagement_date' => $client_engagement["engagement_date"],
						'platform_id' => $client_engagement["platform_id"],
						'details' => $client_engagement["details"],
						'added_by' => Auth::user()->id,
					]);

					$kept_client_engagements[] = $new_client_engagement->id;
				} else {
					$old_client_engagement = ClientEngagement::findOrfail($client_engagement["id"]);
					$old_client_engagement->engagement_id = $client_engagement["engagement_id"];
					$old_client_engagement->engagement_date = $client_engagement["engagement_date"];
					$old_client_engagement->platform_id = $client_engagement["platform_id"];
					$old_client_engagement->details = $client_engagement["details"];
					$old_client_engagement->save();

					$kept_client_engagements[] = $old_client_engagement->id;
				}
			}
		}

		//Delete contact unincluded
		foreach ($existing_client_engagements as $delete_client_engagement) {
			if (in_array($delete_client_engagement, $kept_client_engagements))
				continue;

			$client_engagement = ClientEngagement::findOrfail($delete_client_engagement);
			$client_engagement->delete();
		}
	}

	public function put_attachments($profile, $data)
	{
		$request = request();

		$existing_attachments = $profile->attachments->pluck('id')->toArray();
		$kept_attachments = [];

		if (isset($data["attachments_info"])) {
			$attachments_info = json_decode($data["attachments_info"], true);

			foreach ($attachments_info as $index => $attachment) {
				if (!isset($attachment["id"])) {
					if ($request->hasFile('attachments_' . $index)) {
						$new_attachment = Attachment::create([
							'profile_id' => $profile->id,
							'file_url' => '',
							'description' => $attachment["description"],
							'document_category_id' => $attachment["document_category_id"],
							'document_type_id' => $attachment["document_type_id"],
							'added_by' => Auth::user()->id,
							'tracker_notes' => $attachment["tracker_notes"],
						]);

						$file = $request->file('attachments_' . $index);
						$this->uploadFile($new_attachment, $file);
						$kept_attachments[] = $new_attachment->id;
					}
				} else {
					$old_attachment = Attachment::findOrfail($attachment["id"]);
					if ($old_attachment && $request->hasFile('attachments_' . $index)) {
						$file = $request->file('attachments_' . $index);
						$this->uploadFile($old_attachment, $file);
					}
					$old_attachment->description = $attachment["description"];
					$old_attachment->document_category_id = $attachment["document_category_id"];
					$old_attachment->document_type_id = $attachment["document_type_id"];
					$old_attachment->tracker_notes = $attachment["tracker_notes"];
					$old_attachment->save();
					
					$kept_attachments[] = $old_attachment->id;
				}
			}
		}

		//Delete contact unincluded
		foreach ($existing_attachments as $delete_attachment) {
			if (in_array($delete_attachment, $kept_attachments))
				continue;

			$attachment = Attachment::findOrfail($delete_attachment);
			$this->deleteAttachment($attachment);
		}
	}

	public function add_client_engagement($profile_id, array $data): ClientEngagement
	{
		return DB::transaction(function () use ($profile_id, $data) {
			$client_engagement = ClientEngagement::create([
				'profile_id' => $profile_id,
				'engagement_id' => $data["engagement_id"],
				'engagement_date' => $data["engagement_date"],
				'platform_id' => $data["platform_id"],
				'details' => $data["details"],
				'added_by' => Auth::user()->id,
			]);

			return $client_engagement;
		});
	}

	public function add_contact($profile_id, array $data): Contact
	{
		return DB::transaction(function () use ($profile_id, $data) {
			$contact = Contact::create([
				'profile_id' => $profile_id,
				'contact_type_id' => $data["contact_type_id"],
				'contact_info' => $data["contact_info"],
				'created_by' => Auth::user()->id,
			]);

			return $contact;
		});
	}

	public function add_attachment($profile_id, $index, $data): Attachment
	{
		return DB::transaction(function () use ($profile_id, $index, $data) {
			$attachment = null;
			$request = request();
			
			if ($request->hasFile('attachments_' . $index)) {
				$attachment = Attachment::create([
					'profile_id' => $profile_id,
					'file_url' => '',
					'description' => $data->description,
					'document_category_id' => $data->document_category_id,
					'document_type_id' => $data->document_type_id,
					'tracker_notes' => $data->tracker_notes
				]);

				$file = $request->file('attachments_' . $index);
				$this->uploadFile($attachment, $file);
			}

			return $attachment;
		});
	}

	public function add_notes($profile_id, array $data)
	{
		return DB::transaction(function () use ($profile_id, $data) {
			$profile = $this->getById($profile_id);

			$notes = $profile->notes;

			if (isset($data)) {
				foreach ($data as $index => $note) {
					$note["date_added"] = date("Y-m-d H:i:s");
					$note["created_by"] = Auth::user()->id;
					$note["index"] = count($notes);

					$notes[] = $note;
				}
			}


			$profile->notes = json_encode($notes);
			$profile->save();

			return $profile;
		});
	}

	public function update_note($id, array $data)
	{
		return DB::transaction(function () use ($id, $data) {
			$profile = Profile::findOrfail($id);
			// dd(collect($profile->notes)[1]);
			$notes = $profile->notes;
			// dd($notes);
			$note = [
				"date_requested" => $data["date_requested"],
				"notes" => $data["notes"]
			];

			$notes[$data["index"]]->date_requested = $note["date_requested"];
			$notes[$data["index"]]->notes = $note["notes"];

			$profile->notes = json_encode($notes);
			// dd($profile->notes);
			$profile->save();
			$profile->refresh();

			return $profile->notes;
		});
	}

	public function delete_note($id, $data)
	{
		return DB::transaction(function () use ($id, $data) {
			$profile = Profile::findOrfail($id);
			$notes = $profile->notes;

			unset($notes[$data["index"]]);
			$notes = array_values($notes);
			$notes = collect($notes)->map(function ($note, $key) {
				$note->index = $key;
				return $note;
			});
			
			$profile->notes = json_encode($notes);
			$profile->save();
			$profile->refresh();

			return $profile->notes;
		});
	}

	public function update_contact($id, array $data)
	{
		return DB::transaction(function () use ($id, $data) {
			$contact = Contact::findOrfail($id);
			$contact->contact_type_id = $data["contact_type_id"];
			$contact->contact_info = $data["contact_info"];
			$contact->save();
			$contact->refresh();

			$profile = Profile::findOrfail($contact->profile_id);
			return $profile->contacts;
		});
	}

	public function update_attachment($id, $data)
	{
		return DB::transaction(function () use ($id, $data) {
			$request = request();
			
			$attachment = Attachment::findOrfail($id);
			$attachment->description = $data->description;
			$attachment->document_category_id = $data->document_category_id;
			$attachment->document_type_id = $data->document_type_id;
			$attachment->tracker_notes = $data->tracker_notes;
			
			$attachment->save();

			if ($attachment && $request->hasFile('attachment')) {
				$file = $request->file('attachment');
				$this->uploadFile($attachment, $file);
			}

			$profile = Profile::findOrfail($attachment->profile_id);
			return $profile->attachments;
		});
	}

	public function delete_contact($id)
	{
		return DB::transaction(function () use ($id) {
			$contact = Contact::findOrfail($id);
			$contact->delete();

			$profile = Profile::findOrfail($contact->profile_id);
			return $profile->contacts;
		});
	}

	public function delete_attachment($attachment_info)
	{
		return DB::transaction(function () use ($attachment_info) {

			$attachment = Attachment::findOrfail($attachment_info["id"]);

			$this->deleteAttachment($attachment);
			
			$profile = Profile::findOrfail($attachment->profile_id);
			return $profile->attachments;
		});
	}

	public function update_client_engagement($id, array $data)
	{
		return DB::transaction(function () use ($id, $data) {
			$client_engagement = ClientEngagement::findOrfail($id);
			$client_engagement->engagement_id = $data["engagement_id"];
			$client_engagement->engagement_date = $data["engagement_date"];
			$client_engagement->platform_id = $data["platform_id"];
			$client_engagement->details = $data["details"];
			$client_engagement->save();

			$profile = Profile::findOrfail($client_engagement->profile_id);
			return $profile->client_engagements;
		});
	}

	public function delete_client_engagement($id)
	{
		return DB::transaction(function () use ($id) {
			$client_engagement = ClientEngagement::findOrfail($id);
			$client_engagement->delete();

			$profile = Profile::findOrfail($client_engagement->profile_id);
			return $profile->client_engagements;
		});
	}

	public function uploadFile($attachment, $file)
	{
	   $file_upload = new UploadHelper([
		  'driver' => 'local',
		  'id' => $attachment->id,
		  's3_storage_path' => 'customer-care-tool',
		  's3_folder_path' => config('access.s3_path.attachments'),
		  'file' => $file,
	   ]);
 
	   $file_upload->deleteDirectory();
 
	   if ($file_upload->upload()) {
		  $attachment->file_url =  $file_upload->fileUrl();
		  $attachment->save();
	   }
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

	public function delete_customer($id) {
		$customer = Customer::where('profile_id', $id)->delete();

		return $customer;
	}
}
