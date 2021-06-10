<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\PlatformItem;

use App\Models\Brand;

use App\Models\PlatformType;

use App\Models\LanguageValidator;

class PlatformItemController extends Controller
{


    public function index()
    {

        return view('backend.platform.item.index');
    }


    public function getList(Request $request)
    {



        $lang = $request->input('lang');

        $language = 'English';

        if ($lang == 'it') {
            $language = 'Italian';
        }
        if ($lang == 'de') {
            $language = 'German';
        }

        $plateform_item = PlatformItem::leftJoin('brands as b', 'b.id', 'platform.brand')

            ->leftJoin('platform_type as pt', 'pt.id', 'platform.platform_type')

            ->selectRaw("platform.id, platform.brand as brand_id, platform.platform_type as platform_type_id, b.name as brand, pt.name as platform_type, platform.created_at, platform.updated_at")

            ->where('b.organization', \Session::get('organization'))

            ->get();

        $platform = array();

        foreach ($plateform_item as $datas) {

            // PLATFORM TYPE
            $platform_type_name = $this->getNamePlatformType($datas['platform_type_id'], $lang);

            $platform_type_base_name = (!empty($platform_type_name) ? $platform_type_name : $this->getNamePlatformType($datas['platform_type_id'], 'en'));

            if ($platform_type_name == '' && $platform_type_base_name == '') {

                $platform_type_base_name_italy = $this->getNamePlatformType($datas['platform_type_id'], 'it');

                if ($platform_type_base_name_italy !== '') {

                    $platform_type_base_name = $platform_type_base_name_italy;
                } else {

                    $platform_type_base_name = $this->getNamePlatformType($datas['platform_type_id'], 'de');
                }
            }


            array_push($platform, [

                'id' => $datas['id'],

                'brand' => $datas['brand'],

                'platform_type' => $platform_type_base_name,

                'brand_id' => $datas['brand_id'],

                'platform_type_id' => $datas['platform_type_id'],

                'created_at' => date('d/m/Y', strtotime($datas['created_at'])),

                'updated_at' => date('d/m/Y', strtotime($datas['updated_at'])),

                'convertion_platform_type' => (!empty($platform_type_name) ? false : true),

                'language' => $language

            ]);
        }

        $show_brands = Brand::selectRaw("id, name")
            ->where('organization', \Session::get('organization'))
            ->get();

        $platform_type = PlatformType::orderBy('id', 'DESC')->get();

        $show_platform = array();

        foreach ($platform_type as $datas) {

            $name = $this->getNamePlatformType($datas['id'], $lang);

            $base_name = (!empty($name) ? $name : $this->getNamePlatformType($datas['id'], 'en'));

            if ($name == '' && $base_name == '') {

                $base_name_italy = $this->getNamePlatformType($datas['id'], 'it');

                if ($base_name_italy !== '') {

                    $base_name = $base_name_italy;
                } else {

                    $base_name = $this->getNamePlatformType($datas['id'], 'de');
                }
            }

            array_push($show_platform, [

                'id' => $datas['id'],

                'name' => $base_name,

                'convertion' => (!empty($name) ? false : true),

                'language' => $language

            ]);
        }

        return response()->json(['platform_item' => $platform, 'brands' => $show_brands, 'platform_type' => $show_platform]);
    }


    public function postPlatform(Request $request)
    {

        $input = json_decode($request->input('data'));

        $lang = $request->input('lang');

        $platform_check = new PlatformItem;

        if ($platform_check->count() > 0) {

            foreach ($platform_check->get() as $platform_checks) {

                if ($platform_checks['brand'] == $input->brand && $platform_checks['platform_type'] == $input->platform_type) {

                    $message = 'Brand and Platform type ';

                    return response()->json(alert_duplicate($message, $input));
                }
            }
        }

        $formData = array(

            'brand' => $input->brand,

            'platform_type' => $input->platform_type

        );

        $platform = $platform_check->create($formData);

        if ($platform) {

            return response()->json(alert_success('Platforms item ', $platform));
        }
    }


    public function updatePlatform(Request $request)
    {

        $formData = json_decode($request->input('data'));

        if (!empty($formData->id)) {

            $lang = $formData->language;

            $platform_check = new PlatformItem;

            if ($platform_check->count() > 0) {

                foreach ($platform_check->get() as $platform_checks) {

                    if ($platform_checks['brand'] == $formData->brand && $platform_checks['platform_type'] == $formData->platform_type) {

                        $message = 'Brand and Platform type ';

                        return response()->json(alert_duplicate($message, $formData));
                    }
                }
            }

            $respQuestion = $platform_check->changeData($request);

            if ($respQuestion) {

                return response()->json(alert_success('Platforms item ', $respQuestion));
            }
        }
    }


    public function deletePlatform(Request $request, $id)
    {

        $lang = $request->input('lang');

        $platform = PlatformItem::whereId($id);

        if ($platform->delete()) {

            $message = 'Brand and Platform type ';

            return response()->json(alert_delete($message));
        }
    }


    public function getPlatformName(Request $request, $id, $lang)
    {

        $platform = PlatformItem::whereId($id)->first();

        $message = ucwords(string_to_value($lang, $platform->name));

        return response()->json(['name' => $message]);
    }


    public function show_data($lang)
    {

        $platform = PlatformItem::leftJoin('brands as b', 'b.id', 'platform.brand')

            ->leftJoin('platform_type as pt', 'pt.id', 'platform.platform_type')

            ->selectRaw("platform.id, platform.brand as brand_id, platform.platform_type as platform_type_id, b.name as brand, pt.name as platform_type, platform.created_at, platform.updated_at")

            ->get();

        $language_setting = new LanguageValidator;

        $json = $language_setting->get_data($lang, ['id', 'brand', 'brand_id', 'platform_type_id', 'platform_type', 'created_at', 'updated_at'], $platform);

        return $json;
    }

    public function show_brands($lang)
    {

        $brands = Brand::selectRaw("id, name")->get();

        // $language_setting = new LanguageValidator;

        // $json = $language_setting -> get_data( $lang  , [ 'id', 'name' ], $brands );

        return $brands;
    }

    public function show_platform($lang)
    {

        $type = PlatformType::selectRaw("id, name")->get();

        $language_setting = new LanguageValidator;

        $json = $language_setting->get_data($lang, ['id', 'name'], $type);

        return $json;
    }

    public function getNamePlatformType($id, $lang)
    {

        $platform_type = PlatformType::whereId($id)->first();

        $name = ucwords(string_to_value($lang, $platform_type->name));

        return $name;
    }

    public function getNameBrand($id, $lang)
    {

        $brand = Brand::whereId($id)->first();

        $name = ucwords($brand->name);

        return $name;
    }
}
