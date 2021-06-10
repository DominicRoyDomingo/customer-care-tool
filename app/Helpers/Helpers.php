<?php


// get all route files
if (!function_exists('include_route_files')) {
    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     */
    function include_route_files($folder)
    {
        try {
            $rdi = new recursiveDirectoryIterator($folder);
            $it = new recursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (!$it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}


//  String to slug
if (!function_exists('str_slug')) {
    /**
     * String to Slug
     *
     * @param $string
     */
    function str_slug($string)
    {
        return Str::slug($string);
    }
}


//  String to slug
if (!function_exists('str_random')) {
    /**
     * String to Slug
     *
     * @param $string
     */
    function str_random($num = 100)
    {
        return Str::random($num);
    }
}

/* =====> For multi-language helper function  <===== */

//  form data to json string
if (!function_exists('string_to_json')) {
    function string_to_json($lang, $fields = [], $formData = [])
    {
        $data = [];
        foreach ($formData as $key => $value) {
            $json = $value;
            if (in_array($key, $fields)) {
                $json = string_add_json($lang, $value, []);
            }
            $data[$key] = $json;
        }
        return $data;
    }
}

// get current language
if (!function_exists('lang')) {
    function lang()
    {
        // return 'en';
        return (!is_null(session()->get('locale'))) ? session()->get('locale') : 'en';
    }
}


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

// return json data to array
if (!function_exists('object_to_array')) {
    function object_to_array($lang = '', $fields, $items = array())
    {
        $data = [];
        if (!empty($items)) {
            foreach ($items as $ik => $item) {
                $key = false;
                $object = [];
                foreach ($fields as $field) {
                    $array = string_to_array($item->$field);
                    if (is_array($array)) {
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


// convert string to array
if (!function_exists('string_to_array')) {
    function string_to_array($string)
    {
        $arr = json_decode($string, true);
        return $arr;
    }
}


// array merge to json format
if (!function_exists('string_add_json')) {
    function string_add_json($key, $value, $arrList = [])
    {
        $array = [];

        if (!empty($arrList)) {

            $array = array_merge($arrList, [$key => $value]);
        } else {
            $array = [
                $key => $value
            ];
        }

        return json_encode($array);
    }
}

// Remove string from array
if (!function_exists('string_remove')) {
    function string_remove($lang, $string)
    {
        $arrays = string_to_array($string);

        if (is_array($arrays) and array_key_exists($lang, $arrays))

            unset($arrays[$lang]);

        return $arrays;
    }
}

// Check inf the data is exist
if (!function_exists('is_data_exist')) {
    function is_data_exist($field, $name, $arrayList = [], $withId = false)
    {
        // store id here found duplicate name
        $ids = [];

        foreach ($arrayList as $data) {
            if (strtolower($data[$field]) === strtolower(trim($name))) {
                if ($withId) {
                    $ids[] = $data["id"];
                }else{
                    return true;    
                }
                
            }
        }
        if ($withId) {
            return $ids;
        }else{
            return false;    
        }
    }
}

// Check inf the data is exist ny name
if (!function_exists('get_data_by_name')) {
    function get_data_by_name($field, $name, $arrayList = [])
    {
        foreach ($arrayList as $data) {
            if (strtolower($data[$field]) === strtolower(trim($name))) {
                return $data;
            }
        }
        return null;
    }
}

// Get language

if (!function_exists('getTranslation')) {
    function getTranslation($name, $lang)
    {
        $availableLanguage = array_key_first(json_decode($name, true));

        return isset(json_decode($name)->$lang) ? json_decode($name)->$lang : json_decode($name)->$availableLanguage;
    }
}

if (!function_exists('getTranslationMed4CareBrand')) {
    function getTranslationMed4CareBrand($name, $lang)
    {
        $availableLanguage = array_key_first(json_decode($name, true));

        if(isset(json_decode($name)->it)) {
            return json_decode($name)->it;
        }

        if(isset(json_decode($name)->en)) {
            return json_decode($name)->en;
        }

        return json_decode($name)->$availableLanguage;
    }
}

if (!function_exists('getNewTranslation')) {
    function getNewTranslation($name, $lang)
    {
        $availableLanguage = array_key_first(json_decode($name, true));

        if (isset(json_decode($name)->$lang)) {
            $name = json_decode($name)->$lang;
        } else {
            $new = json_decode($name)->$availableLanguage;
            $name =  $new . ' (No English translation yet)';

            switch ($lang) {
                case 'it':
                    $name = $new . ' (No Italian translation yet)';
                    break;
                case 'de':
                    $name = $new . ' (No German translation yet)';
                    break;
                case 'ph-fil':
                    $name = $new . ' (No Filipino translation yet)';
                    break;
                case 'ph-bis':
                    $name = $new . ' (No Visayan translation yet)';
                    break;
            }
        }

        return $name;
    }
}
