<?php

namespace App\Repositories\Backend;

use App\Exceptions\GeneralException;
use App\Models\Profile;
use App\Models\BrandClient;
use App\Models\Jobs\ClientJob;
use App\Models\Contact;
use App\Models\ClientLocation;
use App\Models\ClientEngagement;
use App\Models\BrandProfileLocation;
use App\Models\BrandProfileJob;
use App\Notifications\Backend\Auth\UserAccountActive;
use App\Notifications\Frontend\Auth\UserNeedsConfirmation;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Auth;

/**
 * Class UserRepository.
 */
class ProfileRepository extends BaseRepository
{
    /**
     * UserRepository constructor.
     *
     * @param  User  $model
     */
    public function __construct(Profile $model)
    {
        $this->model = $model;
    }

    /**
     * @param int    $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return mixed
     */
    public function getPaginated($paged = 25, $orderBy = 'created_at', $sort = 'desc'): LengthAwarePaginator
    {
        return $this->model
            ->active()
            ->orderBy($orderBy, $sort)
            ->paginate($paged);
    }

    /**
     * @param int    $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return LengthAwarePaginator
     */
    public function getDeletedPaginated($paged = 25, $orderBy = 'created_at', $sort = 'desc'): LengthAwarePaginator
    {
        return $this->model
            ->with('roles', 'permissions', 'providers')
            ->onlyTrashed()
            ->orderBy($orderBy, $sort)
            ->paginate($paged);
    }

    /**
     * @param int    $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return mixed
     */
    public function filterByBrand($filter)
    {

        if ($filter != "none") {
            $profiles = $this->model
                ->join('brand_clients', 'profiles.id', '=', 'brand_clients.profile_id')
                ->join('brands', 'brands.id', '=', 'brand_clients.brand_id')
                ->where('brands.id', '=', $filter)
                ->select('profiles.*')
                ->get();
        } else {
            $profiles = $this->model->whereDoesntHave('profile_brands')->get();
        }

        foreach ($profiles as $profile) {
            $profile = $profile->extendedInformation($profile);
        }
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
                foreach ($data["notes"] as $index => $note) {
                    $note["date_added"] = date("Y-m-d H:i:s");
                    $note["created_by"] = Auth::user()->id;
                    $note["index"] = $index;

                    $data["notes"][$index] = $note;
                    //dd($note);
                }
                $data["notes"] = json_encode($data["notes"]);
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

            //Add initial brand
            if (isset($data["brands"])) {
                foreach ($data["brands"] as $brand_id) {
                    if (trim($brand_id) == "") {
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
    public function update(Profile $profile, array $data): Profile
    {

        return DB::transaction(function () use ($profile, $data) {

            if (isset($data["notes"])) {
                foreach ($data["notes"] as $index => $note) {
                    if (!isset($note["date_added"])) {
                        $note["date_added"] = date("Y-m-d H:i:s");
                        $note["created_by"] = Auth::user()->id;
                        $note["index"] = $index;

                        $data["notes"][$index] = $note;
                    }
                }

                $data["notes"] = json_encode($data["notes"]);
            } else {
                $data["notes"] = json_encode([]);
            }

            $this->put_contacts($profile, $data);

            $this->put_location($profile, $data);

            $this->put_jobs($profile, $data);

            $this->put_client_engagements($profile, $data);

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
            $location = $data["location"];
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
            foreach ($data["contacts"] as $contact_data) {
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
            foreach ($data["jobs"] as $job) {
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
            foreach ($data["client_engagements"] as $client_engagement) {
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

    /**
     * getStatsByLocation
     *
     * @param String $type
     * @param int/String $filter
     * @return void
     */
    public function getStatsByLocation($type, $filter)
    {
        $location = array();

        if ($type == 'country') {
            $country = DB::table('brand_profile_locations_vw AS bpl')
                ->selectRaw('bpl.country name, COUNT(1) value')
                ->where('bpl.brand_id', $filter)
                ->groupBy('bpl.country')
                ->orderBy('bpl.country')
                ->get();

            $location = $country;
        } else if ($type == 'province') {
            $province = DB::table('brand_profile_locations_vw AS bpl')
                ->selectRaw('bpl.province name, COUNT(1) value')
                ->where('bpl.brand_id', $filter)
                ->groupBy('bpl.province')
                ->orderBy('bpl.province')
                ->get();

            $location = $province;
        } else if ($type == 'city') {
            $city = DB::table('brand_profile_locations_vw AS bpl')
                ->selectRaw('bpl.city name, COUNT(1) value')
                ->where('bpl.brand_id', $filter)
                ->groupBy('bpl.city')
                ->orderBy('bpl.city')
                ->get();

            $location = $city;
        }

        return $location;
    }


    /**
     * getStatsByJob
     *
     * @param String $type
     * @param int $brand
     * @param String $lang
     * @return void
     */
    public function getStatsByJob($type, $brand, $lang)
    {
        $job = array();
        $json_lang = '$.' . $lang;

        if ($type == 'category') {
            $category = DB::table('brand_profile_jobs_vw AS bpj')
                ->selectRaw("
                    COALESCE (
                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                bpj.category, '" . $json_lang . "'
                            )
                        ), CONCAT( '(No Translation for ', 
                        JSON_UNQUOTE( JSON_EXTRACT( bpj.category, '$.en' ) ), ')')
                    ) name,
                    COUNT(1) value")
                ->where('bpj.brand_id', $brand)
                ->groupBy('bpj.category')
                ->orderBy('bpj.category')
                ->get();

            $job = $category;
        } else if ($type == 'profession') {
            $profession = DB::table('brand_profile_jobs_vw AS bpj')
                ->selectRaw("
                    COALESCE (
                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                bpj.profession, '" . $json_lang . "'
                            )
                        ), CONCAT( '(No Translation for ', 
                        JSON_UNQUOTE( JSON_EXTRACT( bpj.profession, '$.en' ) ), ')')
                    ) name,
                    COUNT(1) value")
                ->where('bpj.brand_id', $brand)
                ->groupBy('bpj.profession')
                ->orderBy('bpj.profession')
                ->get();

            $job = $profession;
        } else if ($type == 'specialization') {
            $specialization = DB::table('brand_profile_jobs_vw AS bpj')
                ->selectRaw("
                    COALESCE (
                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                bpj.specialization, '" . $json_lang . "'
                            )
                        ), CONCAT( '(No Translation for ', 
                        JSON_UNQUOTE( JSON_EXTRACT( bpj.specialization, '$.en' ) ), ')')
                    ) name,
                    COUNT(1) value")
                ->where('bpj.brand_id', $brand)
                ->groupBy('bpj.specialization')
                ->orderBy('bpj.specialization')
                ->get();

            $job = $specialization;
        }

        return $job;
    }

    /**
     * getTabularLocationStats
     *
     * @param int $brand
     * @return void
     */
    public function getTabularLocationStats($brand)
    {
        return BrandProfileLocation::selectRaw(
            "
            CONCAT (
                'Country: ', bpl.country
            ) label,
            COUNT(1) cnt,
            CONCAT(
                '[', 
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        '{\"pv\": \"', bpl.province , 
                        '\", \"ct\": \"', bpl.city, 
                        '\", \"cnt\": ', bpl2.count, 
                        '}'
                    ) ORDER BY IF(bpl.province LIKE '%(No%', 1 , 0), bpl.province ASC, 
                               IF(bpl.city LIKE '%(No%', 1 , 0), bpl.city ASC
                ) , 
                ']'
            ) children
            "
        )
            ->join(DB::raw('(
             SELECT brand_id, country, province, city, COUNT(1) count
             FROM brand_profile_locations_vw 
             GROUP BY brand_id, country, province, city 
            ) AS bpl2'), function ($join) {
                $join->on('bpl.country', 'bpl2.country')
                    ->on('bpl.province', 'bpl2.province')
                    ->on('bpl.city', 'bpl2.city')
                    ->on('bpl.brand_id', 'bpl2.brand_id');
            })
            ->where('bpl.brand_id', $brand)
            ->groupBy('bpl.country')
            ->orderByRaw("IF(bpl.country LIKE '%(No%', 1 , 0), bpl.country ASC")
            ->get();
    }

    /**
     * getTabularJobStats
     *
     * @param int $brand
     * @param String $lang
     * @return void
     */
    public function getTabularJobStats($brand, $lang)
    {
        $json_lang = '$.' . $lang;

        return BrandProfileJob::selectRaw(
            "
            CONCAT (
                'Category: ', COALESCE(
                    JSON_UNQUOTE(
                        JSON_EXTRACT(
                            bpj.category, '" . $json_lang .
                "'
                        )
                    ), '(No Translation)'
                )
            ) label,
            COUNT(1) cnt,
            CONCAT(
                '[', 
                GROUP_CONCAT( 
                    DISTINCT CONCAT( 
                        '{\"pf\": \"', COALESCE(JSON_UNQUOTE(JSON_EXTRACT(bpj.profession, '" . $json_lang .
                "')), '(No Translation)'), 
                        '\", \"sp\": \"', COALESCE(JSON_UNQUOTE(JSON_EXTRACT(bpj.specialization, '" . $json_lang .
                "')), '(No Translation)'), 
                        '\", \"cnt\": ', bpj2.count, 
                        '}'
                    ) ORDER BY IF(bpj.profession LIKE '%(No%', 1 , 0), bpj.profession ASC, 
                               IF(bpj.specialization LIKE '%(No%', 1 , 0), bpj.specialization ASC
                ), 
                ']'
            ) children
            "
        )
            ->join(DB::raw('(
             SELECT brand_id, category, profession, specialization, COUNT(1) count
             FROM brand_profile_jobs_vw 
             GROUP BY brand_id, category, profession, specialization 
            ) AS bpj2'), function ($join) {
                $join->on('bpj.category', 'bpj2.category')
                    ->on('bpj.profession', 'bpj2.profession')
                    ->on('bpj.specialization', 'bpj2.specialization')
                    ->on('bpj.brand_id', 'bpj2.brand_id');
            })
            ->where('bpj.brand_id', $brand)
            ->groupBy('bpj.category')
            ->orderByRaw("IF(bpj.category LIKE '%(No%', 1 , 0), bpj.category ASC")
            ->get();
    }

    /**
     * getDetailedJobStats
     *
     * @param int $brand
     * @param String $lang
     * @param String $country
     * @param String $province
     * @param String $city
     * @return void
     */
    public function getDetailedJobStats($brand, $lang, $country, $province, $city)
    {
        $json_lang = '$.' . $lang;

        DB::statement(DB::raw('SET @running_id:=0'));

        return BrandProfileLocation::selectRaw(
            "
            (@running_id:=@running_id+1) AS id,
            CONCAT(
                REGEXP_REPLACE(
                JSON_UNQUOTE(
                    JSON_EXTRACT(
                        category, '" . $json_lang . "'
                    )
                ), '[()]', ''), ' (', REGEXP_REPLACE(JSON_UNQUOTE(JSON_EXTRACT(profession, '" . $json_lang .
                "')), '[()]', ''), ')') AS name,
                COUNT(1) AS count,
                CONCAT(
                    '[{\"id\": ', (@running_id:=@running_id+1), 
                    ', \"name\": \"', 
                        JSON_UNQUOTE(JSON_EXTRACT(specialization, '" . $json_lang . "')),
                    '\", \"children\": [', 
                        GROUP_CONCAT( 
                            CONCAT(
                                '{\"id\": ', (@running_id:=@running_id+1), 
                                ',\"name\": \"', bpl.surname, ', ', bpl.firstname, '\"}'
                            ) ORDER BY bpl.surname, bpl.firstname
                        ), 
                    ']}]'
                ) AS children
            "
        )
            ->leftJoin('brand_profile_jobs_vw AS bpj', function ($leftjoin) {
                $leftjoin->on('bpl.profile_id', 'bpj.profile_id')
                    ->on('bpl.brand_id', 'bpj.brand_id');
            })
            ->where('bpl.brand_id',  $brand)
            ->where('bpl.country', strval($country))
            ->where('bpl.province', strval($province))
            ->where('bpl.city', strval($city))
            ->groupBy('bpj.category', 'bpj.profession', 'bpj.specialization')
            ->orderByRaw("IF(bpj.category LIKE '%(No%', 1 , 0), bpj.category ASC")
            ->orderByRaw("IF(bpj.profession LIKE '%(No%', 1 , 0), bpj.profession ASC")
            ->orderByRaw("IF(bpj.specialization LIKE '%(No%', 1 , 0), bpj.specialization ASC")
            ->get();
    }

    /**
     * getDetailedLocStats
     *
     * @param int $brand
     * @param String $lang
     * @param String $category
     * @param String $profession
     * @param String $specialization
     * @return void
     */
    public function getDetailedLocStats($brand, $lang, $category, $profession, $specialization)
    {
        $json_lang = '$.' . $lang;

        DB::statement(DB::raw('SET @running_id:=0'));

        return BrandProfileJob::selectRaw(
            "
            (@running_id:=@running_id+1) AS id,
            CONCAT(
            	province, ', ',
                country) AS name,
            COUNT(1) AS count,
            CONCAT(
                '[{\"id\": ', (@running_id:=@running_id+1), ', \"name\": \"', 
                    city, 
                '\", \"children\": [', 
                    GROUP_CONCAT( 
                        CONCAT(
                            '{\"id\": ', (@running_id:=@running_id+1), 
                            ',\"name\": \"', bpj.surname, ', ', bpj.firstname, '\"}'
                        ) ORDER BY bpj.surname, bpj.firstname
                    ), 
                ']}]'
            ) AS children
            "
        )
            ->leftJoin('brand_profile_locations_vw AS bpl', function ($leftjoin) {
                $leftjoin->on('bpj.profile_id', 'bpl.profile_id')
                    ->on('bpj.brand_id', 'bpl.brand_id');
            })
            ->where('bpj.brand_id',  $brand)
            ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(bpj.category, "' . $json_lang . '")) = "' . strval($category) . '"')
            ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(bpj.profession, "' . $json_lang . '")) = "' . strval($profession) . '"')
            ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(bpj.specialization, "' . $json_lang . '")) = "' . strval($specialization) . '"')
            ->groupBy('bpl.country', 'bpl.province', 'bpl.city')
            ->orderByRaw("IF(bpl.country LIKE '%(No%', 1 , 0), bpl.country ASC")
            ->orderByRaw("IF(bpl.province LIKE '%(No%', 1 , 0), bpl.province ASC")
            ->orderByRaw("IF(bpl.city LIKE '%(No%', 1 , 0), bpl.city ASC")
            ->get();
    }

    /**
     * exportJobStats
     *
     * @param String $brand
     * @param String $lang
     * @param String $country
     * @param String $province
     * @param String $city
     * @param String $search
     * @return void
     */
    public function exportJobStats(
        $brand,
        $lang,
        $country,
        $province,
        $city,
        $search
    ) {
        $json_lang = '$.' . $lang;

        return BrandProfileLocation::selectRaw(
            "
            CASE
                WHEN LAG(category) OVER (
                    PARTITION BY JSON_UNQUOTE(
                        JSON_EXTRACT(
                            bpj.category, '" . $json_lang . "'
                        )
                    ), JSON_UNQUOTE(
                        JSON_EXTRACT(
                            bpj.profession, '" . $json_lang . "'
                        )
                    ), JSON_UNQUOTE(
                        JSON_EXTRACT(
                            bpj.specialization, '" . $json_lang . "'
                        )
                    )
                ORDER BY
                    IF(
                        bpj.category LIKE '%(No%', 1 , 0
                    ), bpj.category ASC, IF(
                        bpj.profession LIKE '%(No%', 1 , 0
                    ), bpj.profession ASC, IF(
                        bpj.specialization LIKE '%(No%', 1 , 0
                    ), bpj.specialization ASC, CONCAT (
                        bpl.surname, ', ', bpl.firstname
                    ) ASC
                ) IS NULL THEN COUNT(1) OVER (
                    PARTITION BY category, profession, specialization
                )
                ELSE NULL
            END count,
            CASE
                WHEN LAG(category) OVER (
                    PARTITION BY JSON_UNQUOTE(
                        JSON_EXTRACT(
                            bpj.category, '" . $json_lang . "'
                        )
                    ), JSON_UNQUOTE(
                        JSON_EXTRACT(
                            bpj.profession, '" . $json_lang . "'
                        )
                    ), JSON_UNQUOTE(
                        JSON_EXTRACT(
                            bpj.specialization, '" . $json_lang . "'
                        )
                    )
                ORDER BY
                    IF(
                        bpj.category LIKE '%(No%', 1 , 0
                    ), bpj.category ASC, IF(
                        bpj.profession LIKE '%(No%', 1 , 0
                    ), bpj.profession ASC, IF(
                        bpj.specialization LIKE '%(No%', 1 , 0
                    ), bpj.specialization ASC, CONCAT (
                        bpl.surname, ', ', bpl.firstname
                    ) ASC, bpj.profile_id ASC
                ) IS NULL THEN JSON_UNQUOTE(
                    JSON_EXTRACT(
                        bpj.category, '" . $json_lang . "'
                    )
                )
                ELSE NULL
            END category,
            CASE
                WHEN LAG(profession) OVER (
                    PARTITION BY JSON_UNQUOTE(
                        JSON_EXTRACT(
                            bpj.category, '" . $json_lang . "'
                        )
                    ), JSON_UNQUOTE(
                        JSON_EXTRACT(
                            bpj.profession, '" . $json_lang . "'
                        )
                    ), JSON_UNQUOTE(
                        JSON_EXTRACT(
                            bpj.specialization, '" . $json_lang . "'
                        )
                    )
                ORDER BY
                    IF(
                        bpj.category LIKE '%(No%', 1 , 0
                    ), bpj.category ASC, IF(
                        bpj.profession LIKE '%(No%', 1 , 0
                    ), bpj.profession ASC, IF(
                        bpj.specialization LIKE '%(No%', 1 , 0
                    ), bpj.specialization ASC, CONCAT (
                        bpl.surname, ', ', bpl.firstname
                    ) ASC, bpj.profile_id ASC
                ) IS NULL THEN JSON_UNQUOTE(
                    JSON_EXTRACT(
                        bpj.profession, '" . $json_lang . "'
                    )
                )
                ELSE NULL
            END profession,
            CASE
                WHEN LAG(specialization) OVER (
                    PARTITION BY JSON_UNQUOTE(
                        JSON_EXTRACT(
                            bpj.category, '" . $json_lang . "'
                        )
                    ), JSON_UNQUOTE(
                        JSON_EXTRACT(
                            bpj.profession, '" . $json_lang . "'
                        )
                    ), JSON_UNQUOTE(
                        JSON_EXTRACT(
                            bpj.specialization, '" . $json_lang . "'
                        )
                    )
                ORDER BY
                    IF(
                        bpj.category LIKE '%(No%', 1 , 0
                    ), bpj.category ASC, IF(
                        bpj.profession LIKE '%(No%', 1 , 0
                    ), bpj.profession ASC, IF(
                        bpj.specialization LIKE '%(No%', 1 , 0
                    ), bpj.specialization ASC, CONCAT (
                        bpl.surname, ', ', bpl.firstname
                    ) ASC, bpj.profile_id ASC
                ) IS NULL THEN JSON_UNQUOTE(
                    JSON_EXTRACT(
                        bpj.specialization, '" . $json_lang . "'
                    )
                )
                ELSE NULL
            END specialization,
            CONCAT (
                bpj.surname, ', ', bpj.firstname
            ) fullname
            "
        )
            ->leftJoin('brand_profile_jobs_vw AS bpj', function ($leftjoin) {
                $leftjoin->on('bpl.profile_id', 'bpj.profile_id')
                    ->on('bpl.brand_id', 'bpj.brand_id');
            })
            ->where('bpl.brand_id',  $brand)
            ->where('bpl.country', strval($country))
            ->where('bpl.province', strval($province))
            ->where('bpl.city', strval($city))
            ->whereRaw("(
                LOWER(JSON_UNQUOTE(JSON_EXTRACT(bpj.category, '" . $json_lang . "'))) LIKE LOWER('%" . $search . "%')
                OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(bpj.profession, '" . $json_lang . "'))) LIKE LOWER('%" . $search . "%')
                OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(bpj.specialization, '" . $json_lang . "'))) LIKE LOWER('%" . $search . "%')
                OR LOWER (CONCAT (bpl.surname, ', ', bpl.firstname)) LIKE LOWER('%" . $search . "%')
                )")
            ->orderByRaw("IF(bpj.category LIKE '%(No%', 1 , 0), bpj.category ASC")
            ->orderByRaw("IF(bpj.profession LIKE '%(No%', 1 , 0), bpj.profession ASC")
            ->orderByRaw("IF(bpj.specialization LIKE '%(No%', 1 , 0), bpj.specialization ASC")
            ->orderByRaw("CONCAT (bpl.surname, ', ', bpl.firstname) ASC")
            ->orderBy('bpj.profile_id')
            ->get();
    }

    /**
     * exportLocationStats
     *
     * @param String $brand
     * @param String $lang
     * @param String $category
     * @param String $profession
     * @param String $specialization
     * @param String $search
     * @return void
     */
    public function exportLocationStats(
        $brand,
        $lang,
        $category,
        $profession,
        $specialization,
        $search
    ) {
        $json_lang = '$.' . $lang;

        return BrandProfileJob::selectRaw(
            "
            CASE
                WHEN LAG(country) OVER (
                    PARTITION BY country, province, city
                ORDER BY
                    IF(
                        bpl.country LIKE '%(No%', 1 , 0
                    ), bpl.country ASC, IF(
                        bpl.province LIKE '%(No%', 1 , 0
                    ), bpl.province ASC, IF(
                        bpl.city LIKE '%(No%', 1 , 0
                    ), bpl.city ASC, CONCAT (
                        bpl.surname, ', ', bpl.firstname
                    ) ASC, bpl.profile_id ASC
                ) IS NULL THEN COUNT(1) OVER (
                    PARTITION BY country, province, city
                )
                ELSE NULL
            END count,
            CASE
                WHEN LAG(country) OVER (
                    PARTITION BY bpl.country, bpl.province, bpl.city
                ORDER BY
                    IF(
                        bpl.country LIKE '%(No%', 1 , 0
                    ), bpl.country ASC, IF(
                        bpl.province LIKE '%(No%', 1 , 0
                    ), bpl.province ASC, IF(
                        bpl.city LIKE '%(No%', 1 , 0
                    ), bpl.city ASC, CONCAT (
                        bpl.surname, ', ', bpl.firstname
                    ) ASC, bpl.profile_id ASC
                ) IS NULL THEN bpl.country
                ELSE NULL
            END country,
            CASE
                WHEN LAG(province) OVER (
                    PARTITION BY bpl.country, bpl.province, bpl.city
                ORDER BY
                    IF(
                        bpl.country LIKE '%(No%', 1 , 0
                    ), bpl.country ASC, IF(
                        bpl.province LIKE '%(No%', 1 , 0
                    ), bpl.province ASC, IF(
                        bpl.city LIKE '%(No%', 1 , 0
                    ), bpl.city ASC, CONCAT (
                        bpl.surname, ', ', bpl.firstname
                    ) ASC, bpl.profile_id ASC
                ) IS NULL THEN bpl.province
                ELSE NULL
            END province,
            CASE
                WHEN LAG(city) OVER (
                    PARTITION BY bpl.country, bpl.province, bpl.city
                ORDER BY
                    IF(
                        bpl.country LIKE '%(No%', 1 , 0
                    ), bpl.country ASC, IF(
                        bpl.province LIKE '%(No%', 1 , 0
                    ), bpl.province ASC, IF(
                        bpl.city LIKE '%(No%', 1 , 0
                    ), bpl.city ASC, CONCAT (
                        bpl.surname, ', ', bpl.firstname
                    ) ASC, bpl.profile_id ASC
                ) IS NULL THEN bpl.city
                ELSE NULL
            END city,
            CONCAT (
                bpl.surname, ', ', bpl.firstname
            ) fullname
            "
        )
            ->leftJoin('brand_profile_locations_vw AS bpl', function ($leftjoin) {
                $leftjoin->on('bpj.profile_id', 'bpl.profile_id')
                    ->on('bpj.brand_id', 'bpl.brand_id');
            })
            ->where('bpj.brand_id',  $brand)
            ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(bpj.category, "' . $json_lang . '")) = "' . strval($category) . '"')
            ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(bpj.profession, "' . $json_lang . '")) = "' . strval($profession) . '"')
            ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(bpj.specialization, "' . $json_lang . '")) = "' . strval($specialization) . '"')
            ->whereRaw("(
                bpl.country LIKE LOWER('%" . $search . "%')
                OR bpl.province LIKE LOWER('%" . $search . "%')
                OR bpl.city LIKE LOWER('%" . $search . "%')
                OR LOWER (CONCAT (bpl.surname, ', ', bpl.firstname)) LIKE LOWER('%" . $search . "%')
            )")
            ->orderByRaw("IF(bpl.country LIKE '%(No%', 1 , 0), bpl.country ASC")
            ->orderByRaw("IF(bpl.province LIKE '%(No%', 1 , 0), bpl.province ASC")
            ->orderByRaw("IF(bpl.city LIKE '%(No%', 1 , 0), bpl.city ASC")
            ->orderByRaw("CONCAT (bpl.surname, ', ', bpl.firstname) ASC")
            ->orderBy('bpl.profile_id')
            ->get();
    }
}
