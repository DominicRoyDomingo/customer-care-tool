<?php

namespace App\Http\Middleware;

use App\Models\Brand;
use Closure;

class SetOrganization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(\Session::get('organization') == null ) {
            $defaultOrganization = Auth()->user()->organizationUsers->filter(function ($value) {
                return $value->organizationModel->isDefault == 1;
            });
            $organization = "";
            if($defaultOrganization->isEmpty()) {
                $organization = Auth()->user()->organizationUsers()->first()->organization;
                $defaultOrganization = Auth()->user()->organizationUsers();
            } else {
                $organization = $defaultOrganization->first()->organization;
            }
           
            $brands = Brand::where('organization', $organization)->get();
            if(!$brands->isEmpty()) {
                $brand = $brands->first(function($brand) {
                    return $brand->isDefault == 1;
                });
                if($brand == null) {
                    $brand = $brands->first();
                }
                // $brandObj = array();
                // $brandObj['id'] = $brand->id;
                // $brandObj['name'] = $brand->name;
                // $brandObj = collect($brandObj);
                \Session::put('brand', $brand);
            }
            \Session::put('active_organization', $defaultOrganization->first());
            \Session::put('organization', $organization);
            
            
        } 

        return $next($request);
    }
}
