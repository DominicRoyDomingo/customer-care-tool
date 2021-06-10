<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Attachment;
use App\Models\Contact;
use App\Models\Brand;
use App\Models\ClientLocation;
use App\Models\ClientEngagement;
use App\Models\Auth\User;
use App\Models\Jobs\ClientJob;

class Profile extends Model
{
    protected $guarded = [];
    protected $with = ["client_engagements",  "profile_brands", "contacts", "attachments"];

    protected $appends = [
        'profile_name', 'full_name', 'brand_names', 'country_name', 'province_name',
        'city_name', 'job_data', 'job_category_name', 'job_profession_name', 'job_description_name', 'jobs_display',
        'brand_ids', 'location_display', 'is_loading'
    ];

    public function getIsLoadingAttribute()
    {
        return false;
    }

    public function getNotesAttribute($value)
    {
        $display_format = "d F Y";
        $notes = json_decode($value);

        foreach ($notes as $index => $note) {
            if (!isset($note->index)) {
                $note->index = $index;
            } else {
                $note->index = (int) $note->index;
            }

            $added_by = User::findOrfail($note->created_by);
            $note->added_by = $added_by->full_name;

            $note->date_requested_display = date($display_format, strtotime($note->date_requested));
            $note->date_requested_order = date("YmdHis", strtotime($note->date_requested));
            $note->date_requested = date("Y-m-d", strtotime($note->date_requested));

            //$note->note_display = str_replace("\n", "<br>", $note->notes);
            if (isset($note->notes))
                $note->note_display = nl2br($note->notes);

            $note->date_added_display = date($display_format, strtotime($note->date_added));
            $note->date_added_order = date("YmdHis", strtotime($note->date_added));
            $note->date_added = date("Y-m-d", strtotime($note->date_added));
        }


        //$profile->notes = json_encode($notes);
        return $notes;
    }

    public function getProfileNameAttribute()
    {
        $first = $this->firstname ?: '';
        $last = $this->surname ?: '';
        $middle = $this->middlename ?: '';

        return "{$last} {$first}, {$middle}";
    }

    public function getFullNameAttribute()
    {
        if ($this->firstname == "") {
            return $this->surname;
        }

        $name =  $this->surname . ", " . $this->firstname;
        if ($this->middlename != "")
            $name .= " " . ucfirst(substr($this->middlename, 0, 1)) . ".";
        return $name;
    }

    public function getAltFullNameAttribute()
    {
        $name =  $this->firstname . " " . $this->surname;
        return $name;
    }


    public function getBrandsAttribute()
    {
        $profile_brands = $this->profile_brands;

        $brands = [];

        foreach ($profile_brands as $profile_brand) {
            $brand = Brand::findOrfail($profile_brand->brand_id);
            $brands[] = $brand;
        }

        return $brands;
    }

    public function getBrandIdsAttribute()
    {
        $brands = $this->brands;

        $brand_ids = [];

        foreach ($brands as $brand) {
            $brand_ids[] = $brand->id;
        }

        return $brand_ids;
    }

    public function getBrandNamesAttribute()
    {
        $brands = $this->brands;

        $brand_names = [];

        foreach ($brands as $brand) {
            $brand_names[] = $brand->name;
        }

        $name = implode(", ", $brand_names);
        return $name;
    }

    public function getCountryNameAttribute()
    {
        return $this->location_data->country_display;
    }

    public function getProvinceNameAttribute()
    {
        return $this->location_data->province_display;
    }

    public function getCityNameAttribute()
    {
        return $this->location_data->city_display;
    }

    public function getLocationDisplayAttribute()
    {
        return $this->location_data->location_display;
    }

    public function getLocationDataAttribute()
    {
        $location = new \stdClass();

        $location->country_display = "N/A";
        $location->province_display = "N/A";
        $location->city_display = "N/A";
        $location->location_display = "N/A";

        if (isset($this->client_location)) {
            $location->location_display = $this->client_location->location_display;

            if (isset($this->client_location->country)) {
                $location->country_display = $this->client_location->country->name;
            }
            if (isset($this->client_location->province)) {
                $location->province_display = $this->client_location->province->name;
            }
            if (isset($this->client_location->city)) {
                $location->city_display = $this->client_location->city->name;
            }
        }

        return $location;
    }

    public function getJobDisplayNameAttribute()
    {
        return $this->job_category_name . " " . $this->job_profession_name . " " . $this->job_description_name;
    }

    public function getJobCategoryNameAttribute()
    {
        return $this->job_data->job_category_display;
    }

    public function getJobProfessionNameAttribute()
    {
        return $this->job_data->job_profession_display;
    }

    public function getJobDescriptionNameAttribute()
    {
        return $this->job_data->job_description_display;
    }

    public function getJobsDisplayAttribute()
    {
        return $this->job_data->jobs_display;
    }

    public function getJobDataAttribute()
    {
        $job = new \stdClass();

        $job->job_display = "N/A";
        $job->job_category_display = "N/A";
        $job->job_profession_display = "N/A";
        $job->job_description_display = "N/A";

        $job->jobs_display = [];

        if (isset($this->jobs)) {
            if (count($this->jobs) > 0) {
                $categories = [];
                $professions = [];
                $descriptions = [];
                $jobs_array = [];

                foreach ($this->jobs as $job_instance) {
                    $job_display = "N/A";

                    $categories[] = $job_instance->category->category_name;

                    if (isset($job_instance->profession)) {
                        $professions[] = $job_instance->profession->profession_name;
                        $job_display = $job_instance->profession->profession_name;
                    }

                    if (isset($job_instance->description)) {
                        $descriptions[] = $job_instance->description->description_name;
                        $job_display .= ", " . $job_instance->description->description_name;
                    }

                    $jobs_array[] = $job_display;
                }

                $job->job_category_display = implode(", ", $categories);
                $job->job_profession_display = implode(", ", $professions);

                $job->jobs_display = $jobs_array;

                if (count($descriptions) > 0) {
                    $job->job_description_display = implode(", ", $descriptions);
                } else {
                    $job->job_description_display = "N/A";
                }
            }
        }

        return $job;
    }

    public function client_engagements()
    {
        return $this->hasMany(ClientEngagement::class);
    }

    public function client_location()
    {
        return $this->hasOne(ClientLocation::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function jobs()
    {
        return $this->hasMany(ClientJob::class);
    }

    public function profile_brands()
    {
        return $this->hasMany(BrandClient::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    /*
    public function extendedInformation($profile)
    {
        if (isset($profile->client_location)) {
            $profile->client_location = $profile->client_location;
            $profile->location_display = $profile->client_location->location_display;

            if (isset($profile->client_location->country)) {
                $profile->country_display = $profile->client_location->country->name;
            }
            if (isset($profile->client_location->province)) {
                $profile->province_display = $profile->client_location->province->name;
            }
            if (isset($profile->client_location->city)) {
                $profile->city_display = $profile->client_location->city->name;
            }
        } else {
            $profile->country_display = "";
            $profile->province_display = "";
            $profile->city_display = "";
            $profile->location_display = "N/A";
        }

        $profile->job_category_display = "";
        $profile->job_profession_display = "";
        $profile->job_description_display = "";
        $profile->jobs_display = [];

        if (isset($profile->jobs)) {
            if (count($profile->jobs) > 0) {
                $categories = [];
                $professions = [];
                $descriptions = [];
                $jobs_array = [];

                foreach ($profile->jobs as $job) {
                    $job_display = "N/A";

                    $categories[] = $job->category->category_name;

                    if (isset($job->profession)) {
                        $professions[] = $job->profession->profession_name;
                        $job_display = $job->profession->profession_name;
                    }

                    if (isset($job->description)) {
                        $descriptions[] = $job->description->description_name;
                        $job_display .= ", " . $job->description->description_name;
                    }

                    $jobs_array[] = $job_display;
                }

                $profile->job_category_display = implode(", ", $categories);
                $profile->job_profession_display = implode(", ", $professions);

                $profile->jobs_display = $jobs_array;

                if (count($descriptions) > 0) {
                    $profile->job_description_display = implode(", ", $descriptions);
                } else {
                    $profile->job_description_display = "N/A";
                }
            } else {
                $profile->job_category_display = "N/A";
                $profile->job_profession_display = "N/A";
                $profile->job_description_display = "N/A";
            }
        } else {
            $profile->job_category_display = "N/A";
            $profile->job_profession_display = "N/A";
            $profile->job_description_display = "N/A";
        }

        $display_format = "d F Y";
        $profile->full_name = $this->full_name;

        //client_engagements
        $profile->client_engagements;


        //Contacts
        $contacts = $this->contacts;

        foreach ($contacts as $contact) {
            $contact = $contact->extendedInformation($contact, false);
        }

        $profile->contacts = $contacts;

        //Jobs
        if (isset($profile->jobs)) {
            $jobs = $profile->jobs;

            foreach ($jobs as $index => $job) {
                $job->index = $index;
                $job = $job->extendedInformation($job);
            }

            $profile->jobs = $jobs;
        }

        //Brands
        $profile_brands = $this->profile_brands;

        $brands = [];
        $brand_names = [];
        $brand_ids = [];

        foreach ($profile_brands as $profile_brand) {
            $brand = Brand::findOrfail($profile_brand->brand_id);
            $brand = $brand->extendedInformation($brand, false);
            $brand_names[] = $brand->name;
            $brand_ids[] = $brand->id;
            $brands[] = $brand;
        }

        $profile->brands = $brands;
        $profile->brand_ids = $brand_ids;
        $profile->brand_names = implode(", ", $brand_names);


        return $profile;
    }
    */
}
