<?php

namespace CRM\API\Job;

use App\Models\Brand\BrandCategory;
use App\Models\Jobs\JobCategory;
use App\Models\Jobs\JobCategoryProfession;
use App\Models\Jobs\JobDescription;
use App\Models\Jobs\JobProfession;
use App\Repositories\BaseRepository;
use CRM\API\Job\Traits\CategoryTrait;
use Illuminate\Support\Facades\DB;

class JobRepository extends BaseRepository
{
   use CategoryTrait;

   public function __construct(JobCategory $model)
   {
      $this->model = $model;
   }

   public function getAll($request)
   {
      $data = (object) [];

      if ($request->type === 'category') {

         $data = $this->model
            ->select('job_category.*')
            ->active()
            ->orderBy('created_at', 'asc')
            ->get();
      } else if ($request->type == 'profession') {

         $data = JobProfession::select('job_profession.*')
            // ->when($request->filterHealth, function ($query) use ($request) {
            //    $query->filterHealth($request->filterHealth);
            // })
            ->active()
            ->with(['jobCategoryProfession.JobCategory.brandCategory'])
            ->orderBy('created_at', 'desc')
            ->get();
      } else {
         $data =  JobDescription::select('job_description.*')
            ->with(['jobProfession', 'jobProfession.jobCategoryProfession.JobCategory'])
            ->orderBy('description', 'asc');
         if ($request->filter != null) {
            $newFilter = [];
            foreach ($request->filter as $value) {
               array_push($newFilter, trim(explode('(', $value)[0]));
            }
            foreach ($newFilter as  $category) {
               $data = $data->orWhere(function ($subQuery) use ($category) {
                  $subQuery->whereHas('jobProfession.jobCategoryProfession.JobCategory', function ($query) use ($category) {
                     $query->where('category', 'LIKE', '%' . $category . '%');
                  });
               });
            }
         }
         $data = $data->get();
         $data = $data->map(function ($data) {
            $profession = ucfirst($data->jobProfession->select_profession_name);
            $description = ucfirst($data->select_description_name);
            $data->profession_description = "{$profession}: {$description}";

            return $data;
         });

         // dd($data);
      }

      return $data;
   }

   public function getCategories($request)
   {
      $data = (object) [];


      $data = $this->model->select('job_category.*')
         ->orderBy('category', 'asc')
         ->get();

      return $data;
   }

   public function getFiltered($request)
   {
      $data = (object) [];

      $id = $request->filter_id;

      if ($request->type === 'category') {
         $data = $this->model->select('job_category.*')
            ->orderBy('category', 'asc')
            ->get();
      } else if ($request->type == 'profession') {
         $data = JobProfession::select('job_profession.*')
            ->with(['jobCategoryProfession.JobCategory'])
            ->join('job_category_profession', 'job_category_profession.job_profession_id', '=', 'job_profession.id')
            ->where("job_category_profession.job_category_id", "=", $id)
            ->orderBy('profession', 'asc')
            ->get();
      } else {
         $data =  JobDescription::select('job_description.*')
            ->with(['jobProfession'])
            ->where("job_description.job_profession_id", "=", $id)
            ->orderBy('description', 'asc')
            ->get();
      }

      return $data;
   }

   public function getFilteredJobProfession($request)
   {
      $data = (object) [];
      $data = JobProfession::select('job_profession.*')
         ->with(['jobCategoryProfession.JobCategory'])
         ->orderBy('profession', 'asc');
      $newFilter = [];
      foreach ($request->filter as $value) {
         array_push($newFilter, trim(explode('(', $value)[0]));
      }
      foreach ($newFilter as  $category) {
         $data = $data->orWhere(function ($subQuery) use ($category) {
            $subQuery->whereHas('jobCategoryProfession.JobCategory', function ($query) use ($category) {
               $query->where('category', 'LIKE', '%' . $category . '%');
            });
         });
      }
      $data = $data->get();
      return $data;
   }
   // for catgory
   public function create(array $data)
   {
      return DB::transaction(function () use ($data) {

         $category = $this->model
            ->create([
               'category' => to_json_add(locale(), $data['category'])
            ]);


         if ($category) {
            BrandCategory::create([
               'brand' => $data['brand_id'],
               'category' => $category->id,
            ]);
         }

         return $category;
      });
   }

   public function update($id, array $data): JobCategory
   {
      return DB::transaction(function () use ($id, $data) {

         $lang = $data['locale'];

         $category = $this->getById($id);

         $category->category = to_json_add($lang, $data['category'], to_json_remove($lang, $category->category));

         $category->save();

         return $category;
      });
   }

   // for Profession
   public function create_profession(array $data)
   {
      return DB::transaction(function () use ($data) {
         $jobProfession = JobProfession::create([
            'profession' => to_json_add($data['locale'], $data['profession'])
         ]);

         foreach ($data['category'] as  $id) {
            $jobProfession
               ->jobCategoryProfession()
               ->create([
                  'job_category_id' => $id,
                  'job_profession_id' => $jobProfession->id,
               ]);
         }

         return $jobProfession;
      });
   }

   public function update_profession($id, array $data)
   {
      $lang = $data['locale'];

      $jp = JobProfession::findOrFail($id);

      $jp->profession = to_json_add($lang, $data['profession'], to_json_remove($lang, $jp->profession));

      JobCategoryProfession::where('job_profession_id', $id)->delete();

      foreach ($data['category'] as  $id) {
         JobCategoryProfession::create([
            'job_category_id' => $id,
            'job_profession_id' => $jp->id,
         ]);
      }

      $jp->save();

      return $jp;
   }

   // for Description
   public function create_description(array $data)
   {
      return DB::transaction(function () use ($data) {

         $jd = JobDescription::create([
            'description' => to_json_add($data['locale'], $data['description']),
            'job_profession_id' => $data['profession']
         ]);

         return $jd;
      });
   }

   public function update_description($id, array $data)
   {
      return DB::transaction(function () use ($id, $data) {
         $lang = $data['locale'];

         $jd = JobDescription::findOrFail($id);

         $jd->description = to_json_add($lang, $data['description'], to_json_remove($lang, $jd->description));

         $jd->job_profession_id = $data['profession'];

         $jd->save();

         return $jd;
      });
   }
}
