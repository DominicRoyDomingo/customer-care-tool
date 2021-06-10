<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Exception Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in Exceptions thrown throughout the system.
    | Regardless where it is placed, a button can be listed here so it is easily
    | found in a intuitive way.
    |
    */

    'backend' => [
        'access' => [
            'roles' => [
                'already_exists' => 'That role already exists. Please choose a different name.',
                'cant_delete_admin' => 'You can not delete the Administrator role.',
                'create_error' => 'There was a problem creating this role. Please try again.',
                'delete_error' => 'There was a problem deleting this role. Please try again.',
                'has_users' => 'You can not delete a role with associated users.',
                'needs_permission' => 'You must select at least one permission for this role.',
                'not_found' => 'That role does not exist.',
                'update_error' => 'There was a problem updating this role. Please try again.',
            ],

            'users' => [
                'already_confirmed' => 'This user is already confirmed.',
                'cant_confirm' => 'There was a problem confirming the user account.',
                'cant_deactivate_self' => 'You can not do that to yourself.',
                'cant_delete_admin' => 'You can not delete the super administrator.',
                'cant_delete_self' => 'You can not delete yourself.',
                'cant_delete_own_session' => 'You can not delete your own session.',
                'cant_restore' => 'This user is not deleted so it can not be restored.',
                'cant_unconfirm_admin' => 'You can not un-confirm the super administrator.',
                'cant_unconfirm_self' => 'You can not un-confirm yourself.',
                'create_error' => 'There was a problem creating this user. Please try again.',
                'delete_error' => 'There was a problem deleting this user. Please try again.',
                'delete_first' => 'This user must be deleted first before it can be destroyed permanently.',
                'email_error' => 'That email address belongs to a different user.',
                'mark_error' => 'There was a problem updating this user. Please try again.',
                'not_confirmed' => 'This user is not confirmed.',
                'not_found' => 'That user does not exist.',
                'restore_error' => 'There was a problem restoring this user. Please try again.',
                'role_needed_create' => 'You must choose at lease one role.',
                'role_needed' => 'You must choose at least one role.',
                'social_delete_error' => 'There was a problem removing the social account from the user.',
                'update_error' => 'There was a problem updating this user. Please try again.',
                'update_password_error' => 'There was a problem changing this users password. Please try again.',
            ],

            'contact_types' => [
                'already_confirmed' => 'This contact type is already confirmed.',
                'cant_confirm' => 'There was a problem confirming the contact type account.',
                'cant_deactivate_self' => 'You can not do that to yourself.',
                'cant_delete_admin' => 'You can not delete the super administrator.',
                'cant_delete_self' => 'You can not delete yourself.',
                'cant_delete_own_session' => 'You can not delete your own session.',
                'cant_restore' => 'This contact type is not deleted so it can not be restored.',
                'cant_unconfirm_admin' => 'You can not un-confirm the super administrator.',
                'cant_unconfirm_self' => 'You can not un-confirm yourself.',
                'create_error' => 'There was a problem creating this contact type. Please try again.',
                'delete_error' => 'There was a problem deleting this contact type. Please try again.',
                'delete_first' => 'This contact type must be deleted first before it can be destroyed permanently.',
                'email_error' => 'That email address belongs to a different contact type.',
                'mark_error' => 'There was a problem updating this contact type. Please try again.',
                'not_confirmed' => 'This contact type is not confirmed.',
                'not_found' => 'That contact type does not exist.',
                'restore_error' => 'There was a problem restoring this contact type. Please try again.',
                'role_needed_create' => 'You must choose at lease one role.',
                'role_needed' => 'You must choose at least one role.',
                'social_delete_error' => 'There was a problem removing the social account from the contact type.',
                'update_error' => 'There was a problem updating this contact type. Please try again.',
                'update_password_error' => 'There was a problem changing this contact types password. Please try again.',
            ],

            'brands' => [
                'already_confirmed' => 'This brand is already confirmed.',
                'cant_confirm' => 'There was a problem confirming the brand account.',
                'cant_deactivate_self' => 'You can not do that to yourself.',
                'cant_delete_admin' => 'You can not delete the super administrator.',
                'cant_delete_self' => 'You can not delete yourself.',
                'cant_delete_own_session' => 'You can not delete your own session.',
                'cant_restore' => 'This brand is not deleted so it can not be restored.',
                'cant_unconfirm_admin' => 'You can not un-confirm the super administrator.',
                'cant_unconfirm_self' => 'You can not un-confirm yourself.',
                'create_error' => 'There was a problem creating this brand. Please try again.',
                'delete_error' => 'There was a problem deleting this brand. Please try again.',
                'delete_first' => 'This brand must be deleted first before it can be destroyed permanently.',
                'email_error' => 'That email address belongs to a different brand.',
                'mark_error' => 'There was a problem updating this brand. Please try again.',
                'not_confirmed' => 'This brand is not confirmed.',
                'not_found' => 'That brand does not exist.',
                'restore_error' => 'There was a problem restoring this brand. Please try again.',
                'role_needed_create' => 'You must choose at lease one role.',
                'role_needed' => 'You must choose at least one role.',
                'social_delete_error' => 'There was a problem removing the social account from the brand.',
                'update_error' => 'There was a problem updating this brand. Please try again.',
                'update_password_error' => 'There was a problem changing this brands password. Please try again.',
            ],

            'profiles' => [
                'already_confirmed' => 'This profile is already confirmed.',
                'cant_confirm' => 'There was a problem confirming the profile account.',
                'cant_deactivate_self' => 'You can not do that to yourself.',
                'cant_delete_admin' => 'You can not delete the super administrator.',
                'cant_delete_self' => 'You can not delete yourself.',
                'cant_delete_own_session' => 'You can not delete your own session.',
                'cant_restore' => 'This profile is not deleted so it can not be restored.',
                'cant_unconfirm_admin' => 'You can not un-confirm the super administrator.',
                'cant_unconfirm_self' => 'You can not un-confirm yourself.',
                'create_error' => 'There was a problem creating this profile. Please try again.',
                'delete_error' => 'There was a problem deleting this profile. Please try again.',
                'delete_first' => 'This profile must be deleted first before it can be destroyed permanently.',
                'email_error' => 'That email address belongs to a different profile.',
                'mark_error' => 'There was a problem updating this profile. Please try again.',
                'not_confirmed' => 'This profile is not confirmed.',
                'not_found' => 'That profile does not exist.',
                'restore_error' => 'There was a problem restoring this profile. Please try again.',
                'role_needed_create' => 'You must choose at lease one role.',
                'role_needed' => 'You must choose at least one role.',
                'social_delete_error' => 'There was a problem removing the social account from the profile.',
                'update_error' => 'There was a problem updating this profile. Please try again.',
                'update_password_error' => 'There was a problem changing this profiles password. Please try again.',
            ],

        ],
        
        'general' => [
            'unknown_error' => 'An unknown error occured'
        ]
    ],

    'frontend' => [
        'auth' => [
            'confirmation' => [
                'already_confirmed' => 'Your account is already confirmed.',
                'confirm' => 'Confirm your account!',
                'created_confirm' => 'Your account was successfully created. We have sent you an e-mail to confirm your account.',
                'created_pending' => 'Your account was successfully created and is pending approval. An e-mail will be sent when your account is approved.',
                'mismatch' => 'Your confirmation code does not match.',
                'not_found' => 'That confirmation code does not exist.',
                'pending' => 'Your account is currently pending approval.',
                'resend' => 'Your account is not confirmed. Please click the confirmation link in your e-mail, or <a href=":url">click here</a> to resend the confirmation e-mail.',
                'success' => 'Your account has been successfully confirmed!',
                'resent' => 'A new confirmation e-mail has been sent to the address on file.',
            ],

            'deactivated' => 'Your account has been deactivated.',
            'email_taken' => 'That e-mail address is already taken.',

            'password' => [
                'change_mismatch' => 'That is not your old password.',
                'reset_problem' => 'There was a problem resetting your password. Please resend the password reset email.',
            ],

            'registration_disabled' => 'Registration is currently closed.',
        ],
    ],
];
