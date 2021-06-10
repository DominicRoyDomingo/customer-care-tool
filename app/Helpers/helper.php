<?php

use Carbon\Carbon;

if (!function_exists('language')) {
   function language()
   {
      if (request()->locale == 'en') {
         return 'English';
      } else if (request()->locale == 'it') {
         return 'Italian';
      } else if (request()->locale == 'de') {
         return 'German';
      } else if (request()->locale == 'ph-fil') {
         return 'Filipino';
      } else {
         return 'Visayan';
      }
   }
}

if (!function_exists('locale')) {
   function locale()
   {
      return (!is_null(request()->locale)) ? request()->locale : 'en';
   }
}

if (!function_exists('lang')) {
   function lang()
   {
      return (!is_null(session()->get('locale'))) ? session()->get('locale') : 'en';
   }
}

if (!function_exists('rgb_rand_color')) {
   function rgb_rand_color()
   {
      return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
   }
}

if (!function_exists('hex_rand_color')) {
   function hex_rand_color()
   {
      $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
      $color = '#' . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)];

      return $color;
   }
}

// array merge to json format
if (!function_exists('to_json_add')) {
   function to_json_add($key, $value, $arrList = [])
   {
      $array = [];
      if (!empty($arrList)) {
         $array = array_merge($arrList, [$key => $value]);
      } else {
         $array = [$key => $value];
      }
      return json_encode($array);
   }
}

// return array
if (!function_exists('to_json_remove')) {
   function to_json_remove($lang, $string)
   {
      if (!$string) {
         return [];
      }

      $arrs = to_array($string);

      if (array_key_exists($lang, $arrs)) {
         unset($arrs[$lang]);
      }

      return $arrs;
   }
}


// convert string to array
if (!function_exists('to_array')) {
   function to_array($string)
   {
      $arr = json_decode($string, true);
      return $arr;
   }
}

// return json data object
if (!function_exists('get_data')) {
   function get_data($lang = '', $fields, $items = array(), $withId = false)
   {
      $data = [];
      if (!empty($items)) {
         foreach ($items as $ik => $item) {
            $key = false;
            $object = [];
            foreach ($fields as $field) {
               $array = to_array($item->$field);
               if (is_array($array)) {
                  foreach ($array as $ak => $arr) {
                     if ($lang === $ak) {
                        $object[$field] = $arr;
                        if ($withId) {
                           $object["id"] = $item->id;
                        }
                        
                        $key = true;
                     }
                     if ($lang === '' or $lang === null) {
                        $object[$field] = $arr;
                        if ($withId) {
                           $object["id"] = $item->id;
                        }
                        $key = true;
                     }
                  }
               } else {
                  $object[$field] = $item->$field;
                  if ($withId) {
                     $object["id"] = $item->id;
                  }
               }
            }
            if ($key) {
               $data[] = $object;
            }
         }
      }
      return (object) $data;
   }
}



// Get all languages
if (!function_exists('get_languages')) {
   function get_languages()
   {
      $items = [];
      foreach (array_keys(config('locale.languages')) as $key => $lang) {
         $items[$lang] = __('menus.language-picker.langs.' . $lang);
      }
      return $items;
   }
}

// Get types
if (!function_exists('get_static_types')) {
   function get_static_types($type, $array = array())
   {
      if (!empty($array)) {
         foreach ($array as $key => $values) {
            if (strtolower($type) === strtolower($key)) {
               foreach ($values as $vKey => $value) {
                  if (lang() === $vKey) {
                     return [
                        'en' => $values['en'],
                        'lang' => $value
                     ];
                  }
               }
            }
         }
      }
   }
}

// to json data
if (!function_exists('to_json')) {
   function to_json($fields, $data = [])
   {
      $items = array();
      if ($data->count()) {
         foreach ($data as $value) {
            $object = [];
            foreach ($fields as $field) {
               $array = to_array($value->$field);
               $object[$field] = (is_array($array)) ? $array : $value->$field;
            }
            $items[] = $object;
         }
      }
      return $items;
   }
}

// get static Labels
if (!function_exists('get_labels')) {
   function get_labels()
   {
      return [
         'form' => __('labels.general.form'),
         'jobs' => __('labels.general.jobs'),
         'actions' => __('labels.general.actions'),
         'list' => __('labels.general.list'),
         'category' => __('labels.general.category'),
         'showing' => __('labels.general.showing'),
         'required' => __('labels.general.required'),
         'specialization' => __('labels.general.specialization'),
         'title_type' => __('labels.general.title_type'),
         'country' => __('labels.general.country'),
         'degree' => __('labels.general.degree'),
         'master' => __('labels.general.master'),
         'modules' => __('labels.general.modules'),
         'file_to_import' => __('labels.general.file_to_import'),
         'import' => __('labels.general.import'),
         'leads' => __('labels.general.leads'),

         'phone' => __('labels.general.phone'),
         'birthdate' => __('labels.general.birthdate'),
         'gender' => __('labels.general.gender'),
         'social_media' => __('labels.general.social_media'),
         'profile_url' => __('labels.general.profile_url'),
         'lead_source' => __('labels.general.lead_source'),
         'skip' => ucwords(__('labels.general.skip')),


         'success' => __('alerts.keys.success'),
         'error' => __('alerts.keys.error'),
         'warning' => __('alerts.keys.warning'),


         'prospects' => __('labels.general.prospects'),

         'information' => __('labels.general.information'),

         'start' => __('labels.general.start'),
         'end' => __('labels.general.end'),
         'present' => __('labels.general.present'),
         'to_campaign' => __('labels.general.to_campaign'),

         'status' => __('menus.backend.label.status'),
         'personal_information' => __('menus.backend.label.personal-information'),
         'documents' => __('menus.backend.label.documents'),

         'jobs_experience' => __('labels.general.jobs_experience'),

         'created' => __('labels.backend.access.users.table.created'),
         'attach_job_tags' => __('labels.backend.medical-records.labels.attach_body_parts_tags'),
         'type_description' => __('labels.backend.medical-records.labels.sorting.type_description'),
         'link_description' => __('labels.backend.medical-records.labels.sorting.link_description'),
         'link_to' => __('labels.backend.medical-records.labels.sorting.link_to'),
         'body_part_relation' => __('labels.backend.medical-records.labels.sorting.body_part_relation'),
         'permissions' => __('labels.backend.access.roles.table.permissions'),
         'first_name' => __('labels.backend.access.users.table.first_name'),
         'last_name' => __('labels.backend.access.users.table.last_name'),
         'full_name' => __('labels.backend.access.users.table.full_name'),
         'email' => __('labels.backend.access.users.table.email'),
         'no_data' => __('labels.backend.access.users.table.no_data'),
         'buttons' => [
            'cancel' => __('labels.general.buttons.cancel'),
            'save' => __('labels.general.buttons.save'),
            'edit' => __('labels.general.buttons.edit'),
            'delete' => __('labels.general.buttons.delete'),
            'new' => __('labels.general.buttons.new'),
            'update' => __('labels.general.buttons.update'),
            'search' => __('labels.general.buttons.search'),
            'more' => __('labels.general.buttons.more'),
            'next' => __('labels.general.buttons.next'),
            'remove' => __('labels.general.buttons.remove'),
            'close' => __('labels.general.buttons.close'),
            'yes' => __('labels.general.buttons.yes'),
            'upload' => __('labels.general.buttons.upload'),
            'finish' => __('labels.general.buttons.finish'),
            'view' => __('labels.general.buttons.view'),
         ]
      ];
   }
}

if (!function_exists('check_duplicate')) {
   function check_duplicate($data, $string)
   {
      foreach ($data as $value) {
         if ((array_key_exists("name", $value) && $value['name'] == $string) || (array_key_exists("administration_way", $value) && $value['administration_way'] == $string))
            return true;
      }
      return false;
   }
}

if (!function_exists('get_duplicate')) {
   function get_duplicate($data, $string)
   {
      foreach ($data as $value) {
         if ((array_key_exists("name", $value) && $value['name'] == $string) || (array_key_exists("administration_way", $value) && $value['administration_way'] == $string))
            return $value;
      }
      return false;
   }
}

// return extracted json data
if (!function_exists('get_json_data')) {
   function get_json_data($lang = '', $fields, $items = array())
   {
      $data = [];
      if (!empty($items)) {
         foreach ($items as $ik => $item) {
            $key = false;
            $object = [];
            foreach ($fields as $field) {
               $array = to_array($item->$field);
               if ($field == "substances") {
                  $object[$field] = json_decode($item->$field);
               } else if (is_array($array)) {
                  foreach ($array as $ak => $arr) {
                     if ($lang === $ak) {
                        $object[$field] = $arr;
                        $key = true;
                     }
                     if ($lang === '' or $lang === null) {
                        $object[$field] = $arr;
                        $key = true;
                     }
                  }
               } else {
                  $object[$field] = $item->$field;
               }
            }
            if ($key) {
               $data[] = $object;
            }
         }
      }
      return $data;
   }
}

if (!function_exists('is_data_exist')) {
   function is_data_exist($field, $name, $arrayList = [])
   {
      foreach ($arrayList as $data) {
         if (strtolower($data[$field]) === strtolower(trim($name))) {
            return true;
         }
      }
      return false;
   }
}

// return json data object with default
if (!function_exists('get_data_default')) {
   function get_data_default($lang = '', $fields, $items = array())
   {
      $data = [];
      if (!empty($items)) {
         foreach ($items as $ik => $item) {
            $key = false;
            $object = [];
            foreach ($fields as $field) {
               $array = to_array($item->$field);
               if ($field == "substances") {
                  $object[$field] = json_decode($item->$field);
               } else if (is_array($array)) {
                  foreach ($array as $ak => $arr) {
                     if ($lang === $ak) {
                        $object[$field] = $arr;
                        $key = true;
                     }
                     if ($lang === '' or $lang === null) {
                        $object[$field] = $arr;
                        $key = true;
                     }
                  }
                  if (!$key) {
                     $object[$field] = $array['en'] . " (" . __('labels.general.no') . " " . strtoupper($lang) . " " . __('labels.general.translation') . ")";
                  }
               } else {
                  $object[$field] = $item->$field;
               }
            }
            $data[] = $object;
         }
      }
      return (object) $data;
   }
}

// return array of unique columns
if (!function_exists('get_table_columns')) {
   function get_table_columns($name, $except, $columns = array(), &$data)
   {
      if (!empty($columns)) {
         foreach ($columns as $column) {
            if (in_array($column, $except))
               continue;
            $data[] = $name . "-" . $column;
         }
      }
      return $data;
   }
}

if (!function_exists('get_data_by_name')) {
   function get_data_by_name($value, $arrayList = [])
   {
      foreach ($arrayList as $data) {
         if (strtolower($data['name']) === strtolower(trim($value))) {
            return $data;
         }
      }
      return false;
   }
}

// String to value
if (!function_exists('string_to_value')) {
   function string_to_value($key, $string)
   {
      $array = string_to_array($string);
      if (!empty($array)) {
         foreach ($array as $arKey => $value) {
            if ($arKey === $key) {
               return $value;
            }
         }
      }
      return null;
   }
}

// string to array
if (!function_exists('string_to_array')) {
   function string_to_array($string)
   {
      $arr = json_decode($string, true);
      return $arr;
   }
}
