<?php

namespace CRM\API\Reports;

use App\Http\Controllers\Controller;
use App\Models\StatisticsTable;
use CRM\API\MedicalStuff\MedicalStuffRepository;
use Illuminate\Http\Request;
use CRM\API\Reports\Traits\ReportTrait;

class ReportController extends Controller
{
   use ReportTrait;

   private $model, $termRepo;

   public function __construct(StatisticsTable $model, MedicalStuffRepository $termRepo)
   {
      $this->model    = $model;
      $this->termRepo = $termRepo;
   }

   public function get_insight(Request $request)
   {
      $stats = $this->model->findOrFail($request->id);
      $start = carbon($request->startDate)->format(sql_date_format());
      $end = carbon($request->endDate)->format(sql_date_format());

      $results = $this->queryBetween($stats->name, [
         'start' => $start, 'end' => $end
      ]);

      $items = [];
      if ($results->count() > 0) {
         $items[] = $this->get_data_items($stats->display_name, 'Day', $results);
      }

      return response()->json($items);
   }

   public function get_terms(Request $request)
   {
      $start = carbon($request->startDate)->format(sql_date_format());
      $end = carbon($request->endDate)->format(sql_date_format());

      $items = [];
      $typeName = $request->name === 'term_types' ? 'term_types' : 'specializations';

      foreach (json_decode($request->specification) as $spec) {
         $results = $this->termQueryBetween($start, $end, $spec);
         $itemArray = [];
         foreach ($results as $date => $result) {
            $count = $this->set_items_count($spec->id,  $typeName, $result);
            if ($count > 0) {
               $itemArray[] = (object)[
                  'count' =>  $count,
                  'date' => $date,
                  'date_string' => carbon($date)->toFormattedDateString(),
               ];
            }
         }

         if (!empty($itemArray)) {
            $name =  $request->name === 'term_types' ? $spec->term_type_name : $spec->description_name;
            $items[] = $this->get_data_items($name, 'Day', $itemArray);
         }
      }

      return response()->json($items);
   }

   public function get_data_items($name, $dateName, $data)
   {
      $color = rgb_rand_color();
      $collections = collect($data);

      return [
         'date_name'    => $dateName,
         'display_name' => $name,
         'chart_color'  => $color,
         'summaries'    => $this->summaries($name,  $color,  $collections),
         'items'        => $collections,
      ];
   }

   public function set_items_count($id, $type, $items)
   {
      return collect($items)
         ->map(function ($item) use ($id,  $type) {
            if ($item->$type && in_array($id, string_to_value($type, $item->$type))) {
               return  $item;
            }
         })
         ->filter(function ($item) {
            return $item;
         })
         ->count();
   }
}
