<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrganizationCategory;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationCategoryController extends Controller
{
    public function organizationCategories(Request $request) {
        $lang = \Lang::getLocale();

        $organizationCategories = OrganizationCategory::all()->map(function ($org) use($lang){

            $org['category'] = getTranslation($org['category'], $lang);
        
            return $org;
        
        });

        return response()->json($organizationCategories);
    }

    public function organizations(Request $request) {
        $lang = \Lang::getLocale();

        $organizationCategories = Organization::all();

        return response()->json($organizationCategories);
    }

    public function update(Organization $organization, Request $request) {
        $organization->update([
            'location' => $request->location,
            'other_info' => $request->other_info
        ]);

        return;
    }
}
