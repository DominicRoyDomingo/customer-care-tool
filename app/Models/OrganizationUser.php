<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;
use App\Models\Organization;

class OrganizationUser extends Model
{
    protected $table = 'organization_users';

    protected $appends = ['organization_name'];

    protected $guarded = [];
    //protected $with = ['userModel', 'organizationModel'];

    public function userModel() {
       return $this->belongsTo(User::class, 'user');
    }

    public function organizationModel() {
       return $this->belongsTo(Organization::class, 'organization');
    }

    public function getOrganizationNameAttribute()
    {
        return $this->organizationModel->name;
    }
}
