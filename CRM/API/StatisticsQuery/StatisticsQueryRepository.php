<?php

namespace CRM\API\StatisticsQuery;

use App\Helpers\General\ImageHelper;
use App\Models\StatisticsQuery;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class StatisticsQueryRepository extends BaseRepository
{
   public function __construct(StatisticsQuery $model)
   {
      $this->model = $model;
   }

   public function getAll($request)
   {
         // $statisticsQueries = $statisticsQueries

         /*
            Please uncomment this if this is what you need and comment my code 
            $statisticsQueries = $this->model->select('statisticsQueries.*')->where('organization', '!=' , \Auth::user()->organizations()->first()->id)->with(['categories' => function ($query) {
                  $query->select('id', 'statisticsQuery_id', 'category_id');
            }])->get();
         */
            // dd(\Auth::user()->organization_ids);
      $statisticsQueries = $this->model->all();
      
      return  $statisticsQueries;
   }

   public function create(array $data)
   {
      return DB::transaction(function () use ($data) {
         $request = request();

         $organization = \Auth::user()->organizations()->first()->id;

         $statisticsQuery = $this->model->create([
            'name' => $data['name'],
            'website' => $data['website'],
            'organization' => $organization,
         ]);

         if ($statisticsQuery && $request->hasFile('file')) {

            $file = $request->file('file');

            $this->uploadImage($statisticsQuery, $file);
         }

         return $statisticsQuery;
      });
   }

   public function update($id, array $data): StatisticsQuery
   {
      return DB::transaction(function () use ($id, $data) {

         $request = request();

         $statisticsQuery = $this->getById($id);

         if ($statisticsQuery && $request->hasFile('file')) {

            $file = $request->file('file');

            $this->uploadImage($statisticsQuery, $file);
         }

         $statisticsQuery->name = $data['name'];
         $statisticsQuery->website = $data['website'];

         $statisticsQuery->save();

         return $statisticsQuery;
      });
   }


   public function uploadImage($statisticsQuery, $file)
   {
      $image = new ImageHelper([
         'driver' => 's3',
         'id' => $statisticsQuery->id,
         's3_storage_path' => 'customer-care-tool',
         's3_folder_path' => config('access.s3_path.statisticsQueries'),
         'file' => $file,
      ]);

      $image->deleteDirectory();

      if ($image->upload()) {
         $statisticsQuery->logo =  $image->imageUrl();
         $statisticsQuery->save();
      }
   }
}
