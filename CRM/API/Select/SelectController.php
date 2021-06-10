<?php

namespace CRM\API\Select;

use App\Http\Controllers\Controller;

class SelectController extends Controller
{
   public function renderSelectResponse($model)
   {
      return method_exists($model, 'toSelectResponse') ? $model->toSelectResponse() : $model;
   }

   public function __invoke($type)
   {
      $paginator = Factory::make($type, 'search')
         ->search(request('search'))
         ->simplePaginate(request('per_page'));

      $paginator->setCollection($paginator->getCollection()->map([$this, 'renderSelectResponse']));

      return $paginator;
   }

   public function all($type)
   {
      return Factory::make($type, 'all')->get()->map([$this, 'renderSelectResponse']);
   }

   public function show($type, $id)
   {
      return transform(Factory::make($type, 'show')->findOrFail($id), function ($model) {
         return $this->renderSelectResponse($model);
      });
   }

   public function list_job_descriptions($type)
   {
      return Factory::make($type, 'list')
         ->whereHas('jobProfession.jobCategoryProfession.JobCategory.brandCategory', function ($subQuery) {
            $subQuery
               ->where('brand_category.brand', request()->brand_id);
         })
         ->get()
         ->map([$this, 'renderSelectResponse']);
   }

   public function list_course_types($type)
   {
      return Factory::make($type, 'list')
         // ->whereHas('jobProfession.jobCategoryProfession.JobCategory.brandCategory', function ($subQuery) {
         //    $subQuery
         //       ->where('brand_category.brand', request()->brand_id);
         // })
         ->get()
         ->map([$this, 'renderSelectResponse']);
   }

   public function list($type)
   {
      $ids = request('ids', []);
      return Factory::make($type, 'list')
         ->whereIn(request('key', 'id'), (is_array($ids) ? $ids : [$ids]))
         ->get()
         ->map([$this, 'renderSelectResponse']);
   }

   public function defaults($type)
   {
      return Factory::getDefaults($type);
   }
}
