<?php

namespace CRM\API\StatisticsQuery;

use App\Http\Controllers\Controller;
use App\Models\StatisticsQuery;
use App\Models\StatisticsTable;
use App\Models\StatisticsField;
use App\Models\StatisticsExtension;
use App\Models\Category;
use App\Models\CategoryStatisticsQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class StatisticsQueryController extends Controller
{
   private $statisticsQueryRepository;

   public function __construct(StatisticsQueryRepository $statisticsQueryRepository)
   {
      $this->statisticsQueryRepository = $statisticsQueryRepository;
   }

   public function index()
   {
      $data = StatisticsQuery::all();

      return response()->json($data);
   }

   public function tables()
   {
      $data = StatisticsTable::all();

      return response()->json($data);
   }

   public function fields(Request $request)
   {
      $data = StatisticsTable::findOrfail($request->table);
      $data = $data->fields;

      return response()->json($data);
   }

   public function show(Request $request, $id)
   {
      $statisticsQuery = $this->statisticsQueryRepository->getById($id);

      return response()->json($statisticsQuery);
   }

   public function fetchTableFields(Request $request)
   {
      $fields = DB::select('DESCRIBE ' . $request->table);
      $data = [];

      $restricted_fields = $this->restrictedFields();

      foreach ($fields as $field) {
         if (!in_array($field->Field, $restricted_fields)) {
            $data[] = [
               "name" => $this->unslugify(\ucwords($field->Field)),
               "field" => $field->Field,
            ];
         }
      }

      return response()->json($data);
   }

   public function unslugify($string)
   {
      $string = \str_replace("_", " ", $string);
      return \ucwords($string);
   }

   public function restrictedFields()
   {
      return $restricted_fields = [
         "id",
         "created_at",
         "updated_at"
      ];
   }

   public function fetchData(Request $request)
   {
      $fields = [];

      foreach ($request->fields as $field_id) {
         $field = StatisticsField::findOrfail($field_id);


         if ($field->isForeign) {
            $extension = $field->extension;
         }

         $fields[] = $field;
      }

      $table = StatisticsTable::findOrfail($request->table);
      //$fields = StatisticsField::with(['table', 'extension'])->get();

      $query = $this->buildQuery($request->question, $table->name, $fields, $request->filters, $request->fields_to_show);
      $result = DB::select($query);

      $headers = [];

      $restricted_fields = $this->restrictedFields();

      if (isset($result[0])) {
         foreach ($result[0] as $key => $res) {
            if (!\in_array($key, $restricted_fields)) {
               $headers[] = [
                  "text" => $this->unslugify($key),
                  "value" => $key,
               ];
            }
         }
      }

      $query_results = [
         "query" => $query,
         "headers" => $headers,
         "items" => $result,
      ];

      return response()->json($query_results);
   }


   public function buildQuery($question_type, $table, $fields, $filters, $fields_to_show = [])
   {
      $query = "SELECT ";

      #region Initial fetch
      if ($question_type == "numeric") {
         if (count($fields_to_show) == 0) {
            $query .= " COUNT(*)";
         } else {
            $normal_fields = [];
            $count_fields = [];

            foreach ($fields_to_show as $index => $field) {
               $field_to_show = StatisticsField::findOrfail($field);

               if ($field_to_show->isForeign) {
                  if ($field_to_show->extension->hasChildTable) {
                     $normal_fields[] = $this->selectSecondChild($field_to_show);
                     $count_fields[] = " COUNT(" . $field_to_show->extension->child_table . "." . $field_to_show->extension->child_field . ")";
                  } else {
                     $normal_fields[] = $this->selectFirstChild($field_to_show);
                     $count_fields[] = " COUNT(" . $field_to_show->extension->table . "." . $field_to_show->extension->field . ")";
                  }
               } else {
                  $normal_fields[] = $this->selectParent($field_to_show);
                  $count_fields[] = " COUNT(" . $table . "." . $field_to_show->name . ")";
               }
            }

            $query .= implode(", ", $normal_fields) . ", COUNT(*)";
         }
      } elseif ($question_type == "text") {
         if (count($fields_to_show) > 0) {
            $show_fields = [];

            foreach ($fields_to_show as $index => $field) {
               $field_to_show = StatisticsField::findOrfail($field);

               if ($field_to_show->isForeign) {
                  if ($field_to_show->extension->hasChildTable) {
                     $normal_fields[] = $this->selectSecondChild($field_to_show);
                  } else {
                     $normal_fields[] = $this->selectFirstChild($field_to_show);
                  }
               } else {
                  $normal_fields[] = $this->selectParent($field_to_show);
               }
            }


            $query .= implode(", ", $show_fields);
         }
      }
      #endregion

      #region Load selected fields
      if (count($fields_to_show) == 0) {
         $statistics_table = StatisticsTable::where('name', $table)->first();

         foreach ($statistics_table->fields as $field) {
            if (!in_array($field->id, request()->fields)) {
               $fields[] = $field;
            }
         }
      }

      if (count($fields_to_show) == 0) {
         foreach ($fields as $field) {      //Load each selected field     
            if ($query !=  "SELECT ")
               $query .= ", ";

            if ($field->isForeign) {
               if ($field->extension->hasChildTable) {
                  $query .= $this->selectSecondChild($field);
               } else {
                  $query .= $this->selectFirstChild($field);
               }
            } else {
               $query .= $this->selectParent($field);
            }
         }
      }
      #endregion

      #region Load Table
      $query_tables = [];
      $query_fields = [];
      $query .= " FROM ";
      foreach ($fields as $field) {
         $new_tables = [];
         $join_query = [];
         if (!in_array($field->table->name, $query_tables)) {
            $new_tables[] = $field->table->name;
            $join_query[] = $field->table->name;

            if ($field->isForeign) {
               if (!in_array($field->extension->table, $query_tables)) {  //Check if not already connected
                  $this->joinFirstChild($new_tables, $join_query, $field);
               }

               if ($field->extension->hasChildTable) {
                  if (!in_array($field->extension->child_table, $query_tables)) {  //Check if not already connected
                     $this->joinSecondChild($new_tables, $join_query, $field);
                  }
               }
            }

            $query .= implode("", $join_query);

            $query_tables = array_merge($query_tables, $new_tables);
         } else {
            if ($field->isForeign) {
               if (!in_array($field->extension->table, $query_tables)) {  //Check if not already connected
                  $this->joinFirstChild($new_tables, $join_query, $field);
               }

               if ($field->extension->hasChildTable) {
                  if (!in_array($field->extension->child_table, $query_tables)) {  //Check if not already connected
                     $this->joinSecondChild($new_tables, $join_query, $field);
                  }
               }
            }

            $query .= implode("", $join_query);
            $query_tables = array_merge($query_tables, $new_tables);
         }
      }
      $fs_to_show = [];
      foreach ($fields_to_show as $field_to_show) {
         $field = StatisticsField::findOrfail($field_to_show);
         $fs_to_show[] = $field;
         $new_tables = [];
         $join_query = [];
         if (!in_array($field->table->name, $query_tables)) {
            $new_tables[] = $field->table->name;
            $join_query[] = $field->table->name;

            if ($field->isForeign) {
               if (!in_array($field->extension->table, $query_tables)) {  //Check if not already connected
                  $this->joinFirstChild($new_tables, $join_query, $field);
               }

               if ($field->extension->hasChildTable) {
                  if (!in_array($field->extension->child_table, $query_tables)) {  //Check if not already connected
                     $this->joinSecondChild($new_tables, $join_query, $field);
                  }
               }
            }

            $query .= implode("", $join_query);
            $query_tables = array_merge($query_tables, $new_tables);
         } else {
            if ($field->isForeign) {
               if (!in_array($field->extension->table, $query_tables)) {  //Check if not already connected
                  $this->joinFirstChild($new_tables, $join_query, $field);
               }

               if ($field->extension->hasChildTable) {
                  if (!in_array($field->extension->child_table, $query_tables)) {  //Check if not already connected
                     $this->joinSecondChild($new_tables, $join_query, $field);
                  }
               }
            }

            $query .= implode("", $join_query);
            $query_tables = array_merge($query_tables, $new_tables);
         }
      }
      #endregion

      #region Load Filters
      if (count($filters) > 0) {
         $query .= " WHERE";
         $query_filters = [];

         foreach ($fields as $index => $field) {
            if (count($filters) <= $index)
               break;

            if (count($query_filters) > 0) {
               $query_filters[] = " AND";
            }

            if (!isset($filters[$index]))
               $filters[$index] = "";

            //has Join
            if ($field->isForeign) {
               if ($field->extension->hasChildTable) {
                  $this->filterSecondChild($query_filters, $filters[$index], $field);
               } else {
                  $this->filterFirstChild($query_filters, $filters[$index], $field);
               }
            } else {
               $this->filterParent($query_filters, $filters[$index], $field);
            }
         }

         $query .= implode("", $query_filters);
      }


      $group_by = "";
      $group_filters = [];
      if ($question_type == "numeric") {
         foreach ($fields as $index => $field) {
            if (count($filters) <= $index)
               break;

            $filter = $filters[$index];
            if ($filter == null)
               $filter = "";

            if ($filter == "") {
               if ($group_by == "") {
                  $group_by .=  " GROUP BY";
               }

               if ($field->isForeign) {
                  if ($field->extension->hasChildTable) {
                     $group_filters[] = " " . $field->extension->child_table . "." . $field->extension->child_field;  //has Join
                  } else
                     $group_filters[] = " " . $field->extension->table . "." . $field->extension->field;  //has Join
               } else
                  $group_filters[] = " " . $field->table->name . "." . $field->name;
            }
         }

         if (count($group_filters) > 0) {
            $group_by .= implode(", ", $group_filters);
            $query .= $group_by;
         }
      }
      #endregion

      //dd($query);
      return $query;
   }

   #region SELECT Query
   public function selectQuery($table, $field, $display_name, $multiLang = false)
   {
      $query = "";
      if ($multiLang) {
         $query = " JSON_UNQUOTE(JSON_EXTRACT(" . $table . "." . $field  . ", '$." . locale() . "')) as '" . $display_name . "'";
      } else {
         $query = $table . "." . $field . " as '" . $display_name . "'";
      }

      return $query;
   }

   public function selectParent($field)
   {
      return $this->selectQuery(
         $field->table->name,
         $field->name,
         $field->display_name,
         $field->isMultiLang
      );
   }


   public function selectFirstChild($field)
   {
      return $this->selectQuery(
         $field->extension->table,
         $field->extension->field,
         $field->display_name,
         $field->extension->isMultiLang
      );
   }

   public function selectSecondChild($field)
   {
      return $this->selectQuery(
         $field->extension->child_table,
         $field->extension->child_field,
         $field->display_name,
         $field->extension->isMultiLangChild
      );
   }
   #endregion


   #region FILTER Query
   public function filterQuery($filter, $table, $field, $multiLang)
   {
      $query = "";
      if ($multiLang)
         $query = " JSON_UNQUOTE(JSON_EXTRACT(" . $table . "." . $field . ", '$." . locale() . "')) LIKE '%" .  $filter . "%'";
      else
         $query = " " . $table . "." . $field . " LIKE '%" .  $filter . "%'";

      return $query;
   }

   public function filterParent(&$query_filters, $filter, $field)
   {
      $query_filters[] =  $this->filterQuery(
         $filter,
         $field->table->name,
         $field->name,
         $field->isMultiLang
      );
   }

   public function filterFirstChild(&$query_filters, $filter, $field)
   {
      $query_filters[] =  $this->filterQuery(
         $filter,
         $field->extension->table,
         $field->extension->field,
         $field->extension->isMultiLang
      );
   }

   public function filterSecondChild(&$query_filters, $filter, $field)
   {
      $query_filters[] =  $this->filterQuery(
         $filter,
         $field->extension->child_table,
         $field->extension->child_field,
         $field->extension->isMultiLangChild
      );
   }

   #endregion

   #region JOIN Query
   public function joinQuery($table, $table2, $table2_field)
   {
      return " LEFT JOIN " . $table . " ON " . $table2 . "." . $table2_field . " = " . $table . ".id";
   }

   public function joinFirstChild(&$tables, &$join_query, $field)
   {
      $tables[] = $field->extension->table;

      $join_query[] = $this->joinQuery(
         $field->extension->table,
         $field->table->name,
         $field->name
      );
   }

   public function joinSecondChild(&$tables, &$join_query, $field)
   {
      $tables[] = $field->extension->child_table;

      $join_query[] = $this->joinQuery(
         $field->extension->child_table,
         $field->extension->table,
         $field->extension->field
      );
   }
   #endregion

}
