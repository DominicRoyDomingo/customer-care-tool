<?php

namespace App\Models\Auth;

use App\Models\Auth\Traits\Attribute\UserAttribute;
use App\Models\Auth\Traits\Method\UserMethod;
use App\Models\Auth\Traits\Relationship\UserRelationship;
use App\Models\Auth\Traits\Scope\UserScope;
use App\Models\Organization;
use App\Models\OrganizationUser;

/**
 * Class User.
 */
class User extends BaseUser
{
    use UserAttribute,
        UserMethod,
        UserRelationship,
        UserScope;

        public function full_name(){
            return $this->last_name . " " . $this->first_name;
        }
        
        public function getFullNameAttribute($value)
        {
            return ucfirst($this->last_name) . ' ' . ucfirst($this->first_name);
        }

        public function organization_users() {
            return $this->hasMany(OrganizationUser::class, 'user');
        }

        public function getOrganizationsAttribute(){
            $organizations = [];

            foreach($this->organization_users as $organizationUser){
                $organizations[] = $organizationUser->organizationModel;
            }

            return $organizations;
        }

        public function getOrganizationIdsAttribute(){
            $organization_ids = [];

            foreach($this->organizations as $organization){
                $organization_ids[] = $organization->id;
            }

            return $organization_ids;
        }
}
