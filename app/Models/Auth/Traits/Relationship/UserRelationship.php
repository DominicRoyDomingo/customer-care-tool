<?php

namespace App\Models\Auth\Traits\Relationship;

use App\Models\Auth\PasswordHistory;
use App\Models\Auth\SocialAccount;
use App\Models\Organization;
use App\Models\OrganizationUser;

/**
 * Class UserRelationship.
 */
trait UserRelationship
{
    /**
     * @return mixed
     */
    public function providers()
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * @return mixed
     */
    public function passwordHistories()
    {
        return $this->hasMany(PasswordHistory::class);
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class, 'main_user');
    }

    public function organizationUsers()
    {
        return $this->hasMany(OrganizationUser::class, 'user');
    }
}
