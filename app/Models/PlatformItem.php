<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformItem extends Model
{
    
  protected $table = 'platform';

  protected $guarded = [ 'id' ];

  protected $fillable = [ 'brand', 'platform_type' ];
  protected $appends = ['is_loading', 'platform_item_name' , 'platform_name'];


  public function create( $data ) {

      $platform = new PlatformItem;

      $platform -> brand = $data[ 'brand' ];

      $platform -> platform_type = $data[ 'platform_type' ];

      $platform -> save();

      return $platform;

  }


  public function changeData( $data ){

    $formData = json_decode( $data -> input( 'data' ) );

    $lang = $formData -> language;
    
    $platform = PlatformItem::whereId( $formData -> id ) -> first();

    $platform -> brand = $formData -> brand;
    
    $platform -> platform_type = $formData -> platform_type;

    $platform -> save();

    return $platform;

  }

  public function platformType() {
      return $this->belongsTo(PlatformType::class, 'platform_type');
  }

  public function brand_object() {
      return $this->belongsTo(Brand::class, 'brand');
  }
  
  public function getIsLoadingAttribute()
  {
     return false;
  }
  
  public function getPlatformItemNameAttribute()
  {
     return $this->brand_object->brand_name . " " . $this->platformType->platform_type_name;
  }

  public function getPlatformNameAttribute()
  {

    return $this->platformType->platform_type_name;

  }

  public function getBrandLogoAttribute()
  {

    return $this->brand_object->logo;

  }

}
