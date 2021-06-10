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
                'already_exists' => 'Ang papel na iyon ay mayroon na. Mangyaring pumili ng ibang pangalan.',
                'cant_delete_admin' => 'Hindi mo matatanggal ang tungkulin ng Administrator.',
                'create_error' => 'Mayroong isang problema sa paglikha ng papel na ito. Pakisubukang muli.',
                'delete_error' => 'Mayroong isang problema sa pagtanggal ng papel na ito. Pakisubukang muli.',
                'has_users' => 'Hindi mo matatanggal ang isang papel sa mga nauugnay na gumagamit.',
                'needs_permission' => 'Dapat kang pumili ng kahit isang pahintulot para sa papel na ito.',
                'not_found' => 'Ang papel na iyon ay wala.',
                'update_error' => 'Mayroong isang problema sa pag-update sa papel na ito. Pakisubukang muli.',
            ],

            'users' => [
                'already_confirmed' => 'Ang gumagamit na ito ay nakumpirma na.',
                'cant_confirm' => 'Mayroong problema sa pagkumpirma ng account ng gumagamit.',
                'cant_deactivate_self' => 'Hindi mo magagawa iyon sa iyong sarili.',
                'cant_delete_admin' => 'Hindi mo matatanggal ang super administrator.',
                'cant_delete_self' => 'Hindi mo matatanggal ang iyong sarili.',
                'cant_delete_own_session' => 'Hindi mo matatanggal ang iyong sariling session.',
                'cant_restore' => 'Ang gumagamit na ito ay hindi tinanggal kaya hindi ito maibabalik.',
                'cant_unconfirm_admin' => 'Hindi mo ma-un-confirm ang super administrator.',
                'cant_unconfirm_self' => 'Hindi mo ma-un-confirm ang iyong sarili.',
                'create_error' => 'Mayroong isang problema sa paglikha ng gumagamit na ito. Pakisubukang muli.',
                'delete_error' => 'Mayroong isang problema sa pagtanggal ng gumagamit na ito. Pakisubukang muli.',
                'delete_first' => 'Kailangang tanggalin muna ang gumagamit na ito bago ito tuluyang masira.',
                'email_error' => 'Ang email address na iyon ay kabilang sa ibang gumagamit.',
                'mark_error' => 'Mayroong isang problema sa pag-update sa gumagamit na ito. Pakisubukang muli.',
                'not_confirmed' => 'Ang gumagamit na ito ay hindi nakumpirma.',
                'not_found' => 'Wala ang gumagamit na iyon.',
                'restore_error' => 'Nagkaroon ng problema sa pagpapanumbalik ng gumagamit na ito. Pakisubukang muli.',
                'role_needed_create' => 'Dapat kang pumili sa pag-upa ng isang papel.',
                'role_needed' => 'Dapat kang pumili ng kahit isang papel lang.',
                'social_delete_error' => 'There was a problem removing the social account from the user.Mayroong isang problema sa pag-alis ng social account mula sa gumagamit.',
                'update_error' => 'Mayroong isang problema sa pag-update sa gumagamit na ito. Pakisubukang muli.',
                'update_password_error' => 'Mayroong isang problema sa pagbabago ng password ng mga gumagamit na ito. Pakisubukang muli.',
            ],

            'contact_types' => [
                'already_confirmed' => 'Ang uri ng contact na ito ay nakumpirma na.',
                'cant_confirm' => 'Mayroong isang problema sa pagkumpirma ng account ng uri ng contact.',
                'cant_deactivate_self' => 'Hindi mo magagawa iyon sa iyong sarili.',
                'cant_delete_admin' => 'Hindi mo matatanggal ang super administrator.',
                'cant_delete_self' => 'Hindi mo matatanggal ang iyong sarili.',
                'cant_delete_own_session' => 'Hindi mo matatanggal ang iyong sariling session.',
                'cant_restore' => 'Ang uri ng contact na ito ay hindi tinanggal upang hindi ito maibalik.',
                'cant_unconfirm_admin' => 'Hindi mo ma-un-confirm ang super administrator.',
                'cant_unconfirm_self' => 'Hindi mo ma-un-confirm ang iyong sarili.',
                'create_error' => 'Mayroong isang problema sa paglikha ng ganitong uri ng contact. Pakisubukang muli.',
                'delete_error' => 'Mayroong isang problema sa pagtanggal ng uri ng contact na ito. Pakisubukang muli.',
                'delete_first' => 'Ang uri ng pakikipag-ugnay na ito ay dapat munang matanggal bago ito tuluyang masira.',
                'email_error' => 'Ang email address na iyon ay kabilang sa ibang uri ng contact.',
                'mark_error' => 'Mayroong isang problema sa pag-update sa uri ng contact na ito. Pakisubukang muli.',
                'not_confirmed' => 'Ang uri ng contact na ito ay hindi nakumpirma.',
                'not_found' => 'Ang uri ng contact na iyon ay wala.',
                'restore_error' => 'Mayroong isang problema sa pagpapanumbalik ng ganitong uri ng contact. Pakisubukang muli.',
                'role_needed_create' => 'Dapat kang pumili sa pag-upa ng isang papel.',
                'role_needed' => 'Dapat kang pumili ng kahit isang papel lang.',
                'social_delete_error' => 'Mayroong isang problema sa pag-alis ng social account mula sa uri ng contact.',
                'update_error' => 'Mayroong isang problema sa pag-update sa uri ng contact na ito. Pakisubukang muli.',
                'update_password_error' => 'Mayroong isang problema sa pagbabago ng password ng mga uri ng contact na ito. Pakisubukang muli.',
            ],

            'brands' => [
                'already_confirmed' => 'Ang tatak na ito ay nakumpirma na.',
                'cant_confirm' => 'Mayroong problema sa pagkumpirma ng account ng brand.',
                'cant_deactivate_self' => 'Hindi mo magagawa iyon sa iyong sarili.',
                'cant_delete_admin' => 'Hindi mo matatanggal ang super administrator.',
                'cant_delete_self' => 'Hindi mo matatanggal ang iyong sarili.',
                'cant_delete_own_session' => 'Hindi mo matatanggal ang iyong sariling session.',
                'cant_restore' => 'Ang tatak na ito ay hindi tinanggal kaya hindi ito maibabalik.',
                'cant_unconfirm_admin' => 'Hindi mo ma-un-confirm ang super administrator.',
                'cant_unconfirm_self' => 'Hindi mo ma-un-confirm ang iyong sarili.',
                'create_error' => 'Mayroong isang problema sa paglikha ng tatak na ito. Pakisubukang muli.',
                'delete_error' => 'Mayroong isang problema sa pagtanggal ng tatak na ito. Pakisubukang muli.',
                'delete_first' => 'Dapat munang matanggal ang tatak na ito bago ito tuluyang masira.',
                'email_error' => 'Ang email address na iyon ay kabilang sa ibang tatak.',
                'mark_error' => 'Mayroong isang problema sa pag-update sa tatak na ito. Pakisubukang muli.',
                'not_confirmed' => 'Ang tatak na ito ay hindi nakumpirma.',
                'not_found' => 'Ang tatak na iyon ay wala.',
                'restore_error' => 'Mayroong isang problema sa pagpapanumbalik ng tatak na ito. Pakisubukang muli.',
                'role_needed_create' => 'Dapat kang pumili sa pag-upa ng isang papel.',
                'role_needed' => 'Dapat kang pumili ng kahit isang papel lang.',
                'social_delete_error' => 'Mayroong isang problema sa pag-alis ng social account mula sa tatak.',
                'update_error' => 'Mayroong isang problema sa pag-update sa tatak na ito. Pakisubukang muli.',
                'update_password_error' => 'Mayroong isang problema sa pagbabago ng password ng mga tatak na ito. Pakisubukang muli.',
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
