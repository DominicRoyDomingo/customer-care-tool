<?php

namespace CRM\API\Provider;

use App\Repositories\BaseRepository;
use App\Models\Provider;
use App\Models\ArticleContentMaker\Geolocalization;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ProviderRepository extends BaseRepository
{
   public function findProvider($id) {

      $provider = Provider::with([
         'providers_brand_item',
         'provider_group_item',
         'provider_actors',
         'country_item' => function($query) {
             $query->select('id','name');
          },
          'division_item' => function($query) {
             $query->select('id','name');
          },
          'city_item' => function($query) {
             $query->select('id','name');
          },
          'provider_profiles',
          'provider_services',
          'provider_and_its_type_items',
          'provider_and_its_types',
          'provider_sync_reference',
          'provider_other_infos',
          'payment_plan',
         ])->withCount(['provider_services'])->findOrFail($id);

      return $provider;

   }

   public function updateGeolocalizationAlgolia($country, $division, $city) {
      $geolocalizations = Geolocalization::where('country', $country)->where('division', $division)->where('city', $city)->get();
      foreach ($geolocalizations as $geolocalization) {
         if($geolocalization->article_item != null) {
            if($geolocalization->article_item->geolocalization_sync_reference != null) {
               $geolocalization->article_item->geolocalization_sync_reference()->updateOrCreate([
                  'table_id'   => $geolocalization->article_item->id,
               ],[
                  'sync_class' => 'Geolocalization',
                  'action' => isset($geolocalization->article_item->geolocalization_sync_reference->action) ? $geolocalization->article_item->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New' : 'New',
                  'source_table' => 'geolocalizations',
               ]);
            };
         };
      }
   }
}
