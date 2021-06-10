<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'organizations';

    protected $guarded = [];

    public function organization_users() {
        return $this->hasMany(OrganizationUser::class, 'organization');
    }


    public function getUserIdsAttribute(){
        $user_ids = [];

        foreach($this->users as $user){
            $user_ids[] = $user->id;
        }

        return $user_ids;
    }

    public function getUsersAttribute() {
        $users = [];

        foreach($this->organization_users as $organization_user){
            $users[] = $organization_user->userModel;
        }

        return $users;
    }
}
