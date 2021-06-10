<?php

namespace App\Models;

use App\Models\Actions\Engagement;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;

class ClientEngagement extends Model
{
    //
    protected $guarded = [];
    protected $appends = ['engagement_date_display','created_at_display','engagement_name','platform_name', 'details_display',  'adder_name'];

    public function engagement() {
        return $this->belongsTo(Engagement::class);
    }
    
    public function platform() {
        return $this->belongsTo(PlatformItem::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class, "added_by");
    }

    public function getEngagementNameAttribute(){
        return $this->engagement->engagement_name;
    }

    public function getPlatformNameAttribute(){
        if(isset($this->platform))
            return $this->platform->platform_item_name;
        else
            return "N/A";
    }

    public function getCreatedAtDisplayAttribute(){
        $display_format = "d F Y";
        return date($display_format, strtotime($this->created_at));
     }
  
    public function getEngagementDateDisplayAttribute(){
        $display_format = "d F Y";
        return date($display_format, strtotime($this->engagement_date));
    }

    public function getDetailsDisplayAttribute(){
        return nl2br($this->details);
    }

    public function getAdderNameAttribute(){
        return $this->user->name;
    }
}
