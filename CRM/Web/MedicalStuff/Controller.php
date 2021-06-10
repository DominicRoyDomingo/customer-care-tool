<?php


namespace CRM\Web\MedicalStuff;

use App\Http\Controllers\Controller as ControllersController;
use App\Models\Auth\User;
use App\Models\MedicalStuff\MedicalArticle;
use App\Models\MedicalStuff\MedicalTerm;
use Illuminate\Http\Request;
use Str;

class Controller extends ControllersController
{
    public function term_type(Request $request)
    {
        return view('backend.pages.medical_stuff.term_type');
    }

    public function terms(Request $request)
    {
        return view('backend.pages.medical_stuff.terms');
    }

    public function term_show($id, Request $request)
    {
        $medicalTerm = MedicalTerm::findOrFail($id);

        return view('backend.pages.medical_stuff.term_show')
            ->withMedicalTerm($medicalTerm);
    }

    public function type_authors(Request $request)
    {
        return view('backend.pages.medical_stuff.type_authors');
    }

    public function term_description(Request $request)
    {
        return view('backend.pages.medical_stuff.term_description');
    }

    public function item_index(Request $request)
    {
        // dd('Test Route');
        return view('backend.pages.medical_stuff.articles');
    }

    public function item_show($id, Request $request)
    {
        $article = MedicalArticle::findOrFail($id);

        return view('backend.pages.medical_stuff.publishing_item_show')
            ->withArticle($article);
    }

    public function html_tags_priority(Request $request)
    {
        return view('backend.pages.medical_stuff.html_tags_priority');
    }

    public function type_date(Request $request)
    {
        return view('backend.pages.medical_stuff.type_date');
    }

    public function publishing_manage(Request $request)
    {
        return view('backend.pages.medical_stuff.publishing_type');
    }

    public function prefilledsection_manage(Request $request)
    {
        return view('backend.pages.medical_stuff.publishing_prefilledsection');
    }
}
