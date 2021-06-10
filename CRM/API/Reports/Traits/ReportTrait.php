<?php

namespace CRM\API\Reports\Traits;

use App\Models\MedicalStuff\MedicalTerm;
use Carbon\CarbonPeriod;
use DB;

/**
 * Report Trait controller
 */

trait ReportTrait
{
   public function queryBetween($table, array $date)
   {
      DB::beginTransaction();

      try {
         $tableResult = DB::table($table)
            ->whereBetween('created_at', [$date['start'], $date['end']])
            ->orWhereDate('created_at', $date['end'])
            ->groupBy('date_string')
            ->orderBy('created_at', 'Asc');

         $array = [];

         // Temporary for days 
         // $this->noOfDays($date['start'], $date['end']) > 31
         if (false) {
            $array = [
               DB::raw('DATE_FORMAT(created_at, "%Y-%b") as date_string'),
               DB::raw('MONTHNAME(created_at) as month'),
               DB::raw('YEAR(created_at) AS year'),
               DB::raw('COUNT(*) as count'),
            ];
         } else {
            $array = [
               DB::raw('DATE_FORMAT(created_at, "%M %d, %Y") as date_string'),
               DB::raw('Date(created_at) as date'),
               DB::raw('COUNT(*) as count'),
            ];
         }

         return $tableResult->get($array);

         DB::commit();
      } catch (\Exception $e) {
         DB::rollBack();
      }
   }

   public function termQueryBetween($start, $end, $specification)
   {
      DB::beginTransaction();
      try {

         return DB::table('medical_terms')
            ->select(
               'medical_terms.*',
               DB::raw('DATE_FORMAT(medical_terms.created_at, "%M %d, %Y") as date_string, Date(medical_terms.created_at) as date')
            )
            ->join('brand_terms', function ($join) {
               $join
                  ->on('medical_terms.id', '=', 'brand_terms.terminology_id')
                  ->where('brand_terms.brand_id', request()->brand_id);
            })
            ->whereNotNull(request()->name)
            ->where(function ($query) use ($start, $end) {
               $query
                  ->whereBetween('medical_terms.created_at', [$start, $end])
                  ->orWhereDate('medical_terms.created_at', $end);
            })
            ->orderBy('date', 'Asc')
            ->get()
            ->groupBy(function ($data) {
               return $data->date;
            });

         DB::commit();
      } catch (\Exception $th) {
         DB::rollBack();
      }
   }

   public function summaries($name, $color, $collections)
   {
      $titles = ['average_entries', 'highest_entries', 'total_entries'];
      $items = [];

      foreach ($titles as $key => $title) {
         $value = 0;

         if ($key === 0) {
            $value = $this->getAverage($collections);
         } elseif ($key === 1) {
            $value = $collections->max('count') ?? 0;
         } else {
            $value = $collections->sum('count');
         }

         $items[] = [
            'title' => $name,
            'color' => $color,
            'name' => $title,
            'value' => $value
         ];
      }

      return     $items;
   }

   public function getAverage($data)
   {
      $start = carbon(request()->startDate)->format(sql_date_format());
      $end = carbon(request()->endDate)->format(sql_date_format());
      $dateRanges = CarbonPeriod::create($start,  $end);

      $items = [];

      foreach ($dateRanges as $date) {
         $key = false;
         $item = '';

         foreach ($data as $value) {
            if (carbon($date)->format(sql_date_format()) == $value->date) {
               $item = $value;
               $key = true;
               break;
            }
         }

         $items[] = $key ? $item->count : 0;
      }

      $sum = array_sum($items) / count($items);

      return round($sum, 2);
   }

   public function isMonthly($start, $end)
   {
      if ($start->isSameDay(carbon()->startOfMonth())) {
         return false;
      }

      if ($end->isSameDay(carbon()->endOfMonth())) {
         return false;
      }

      return true;
   }

   public function isYearLy($start, $end)
   {
      return $start->diffInMonths($end, false);
   }

   public function noOfDays($start, $end)
   {
      return $start->diffInDays($end, false);
   }
}
