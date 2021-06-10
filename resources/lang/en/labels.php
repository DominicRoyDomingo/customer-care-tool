<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Labels Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in labels throughout the system.
    | Regardless where it is placed, a label can be listed here so it is easily
    | found in a intuitive way.
    |
    */

    'general' => [
        'all' => 'All',
        'yes' => 'Yes',
        'no' => 'No',
        'copyright' => 'Copyright',
        'custom' => 'Custom',
        'actions' => 'Actions',
        'active' => 'Active',
        'buttons' => [
            'save' => 'Save',
            'update' => 'Update',
        ],
        'hide' => 'Hide',
        'inactive' => 'Inactive',
        'none' => 'None',
        'show' => 'Show',
        'toggle_navigation' => 'Toggle Navigation',
        'create_new' => 'Create New',
        'toolbar_btn_groups' => 'Toolbar with button groups',
        'more' => 'More',
    ],

    'backend' => [
        'access' => [
            'roles' => [
                'platform' => 'Platform Management',
                'tags' => 'Tags Management',
                'brand' => 'Brands Management',
                'dates' => 'Date Statuses Management',
                'content' => 'Content Management',
                'category' => 'Category Management',
                'items' => 'Items Management',
                'workspace' => 'Workspaces',
                'publishing' => [
                    'publishing' => "Publishing Management",
                    'type'       => "Item Type",
                    'item'       => "Publishing Items"
                ],
                'campaign' => "Campaign Management",
                'create' => 'Create Role',
                'edit' => 'Edit Role',
                'management' => 'Role Management',
                'table' => [
                    'number_of_users' => 'Number of Users',
                    'permissions' => 'Permissions',
                    'role' => 'Role',
                    'sort' => 'Sort',
                    'total' => 'role total|roles total',
                ],
            ],

            'users' => [
                'active' => 'Active Users',
                'all_permissions' => 'All Permissions',
                'change_password' => 'Change Password',
                'change_password_for' => 'Change Password for :user',
                'create' => 'Create User',
                'deactivated' => 'Deactivated Users',
                'deleted' => 'Deleted Users',
                'edit' => 'Edit User',
                'management' => 'Team Management',
                'no_permissions' => 'No Permissions',
                'no_roles' => 'No Roles to set.',
                'permissions' => 'Permissions',
                'user_actions' => 'User Actions',
                'invite' => 'Invite Users',

                'table' => [
                    'confirmed' => 'Confirmed',
                    'created' => 'Created',
                    'email' => 'E-mail',
                    'id' => 'ID',
                    'last_updated' => 'Last Updated',
                    'name' => 'Name',
                    'first_name' => 'First Name',
                    'last_name' => 'Last Name',
                    'no_deactivated' => 'No Deactivated Users',
                    'no_deleted' => 'No Deleted Users',
                    'other_permissions' => 'Other Permissions',
                    'permissions' => 'Permissions',
                    'abilities' => 'Abilities',
                    'roles' => 'Roles',
                    'social' => 'Social',
                    'total' => 'user total|users total',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Overview',
                        'history' => 'History',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Confirmed',
                            'created_at' => 'Created At',
                            'deleted_at' => 'Deleted At',
                            'email' => 'E-mail',
                            'last_login_at' => 'Last Login At',
                            'last_login_ip' => 'Last Login IP',
                            'last_updated' => 'Last Updated',
                            'name' => 'Name',
                            'first_name' => 'First Name',
                            'last_name' => 'Last Name',
                            'status' => 'Status',
                            'timezone' => 'Timezone',
                        ],
                    ],
                ],

                'view' => 'View User',
            ],

            'contact_types' => [
                'active' => 'Contact Types',
                'create' => 'Create Contact Type',
                'deactivated' => 'Deactivated Contact Types',
                'deleted' => 'Deleted Contact Types',
                'edit' => 'Edit Contact Type',
                'management' => 'Contact Type Management',
                'no_permissions' => 'No Permissions',
                'no_roles' => 'No Roles to set.',
                'permissions' => 'Permissions',
                'contact_type_actions' => 'Contact Type Actions',

                'table' => [
                    'confirmed' => 'Confirmed',
                    'created' => 'Created',
                    'id' => 'ID',
                    'last_updated' => 'Last Updated',
                    'type_name' => 'Type Name',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Overview',
                        'history' => 'History',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Confirmed',
                            'created_at' => 'Created At',
                            'deleted_at' => 'Deleted At',
                            'email' => 'E-mail',
                            'last_login_at' => 'Last Login At',
                            'last_login_ip' => 'Last Login IP',
                            'last_updated' => 'Last Updated',
                            'name' => 'Name',
                            'first_name' => 'First Name',
                            'last_name' => 'Last Name',
                            'status' => 'Status',
                            'timezone' => 'Timezone',
                        ],
                    ],
                ],

                'forms' => [
                    'title' => 'Contact Type Name',
                    'type_name' => 'Type Name',
                ],

                'view' => 'View Contact Type',
            ],

            'brands' => [
                'active' => 'Brands',
                'create' => 'Create Brand',
                'deactivated' => 'Deactivated Brands',
                'deleted' => 'Deleted Brands',
                'edit' => 'Edit Brand',
                'management' => 'Brand Management',
                'no_permissions' => 'No Permissions',
                'no_roles' => 'No Roles to set.',
                'permissions' => 'Permissions',
                'brand_actions' => 'Brand Actions',

                'table' => [
                    'confirmed' => 'Confirmed',
                    'id' => 'ID',
                    'created_at' => 'Created At',
                    'updated_at' => 'Updated At',
                    'logo' => 'Logo',
                    'brand_name' => 'Brand Name',
                    'website' => 'Website',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Overview',
                        'history' => 'History',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Confirmed',
                            'created_at' => 'Created At',
                            'deleted_at' => 'Deleted At',
                            'email' => 'E-mail',
                            'last_login_at' => 'Last Login At',
                            'last_login_ip' => 'Last Login IP',
                            'last_updated' => 'Last Updated',
                            'name' => 'Name',
                            'first_name' => 'First Name',
                            'last_name' => 'Last Name',
                            'status' => 'Status',
                            'timezone' => 'Timezone',
                        ],
                    ],
                ],

                'forms' => [
                    'title' => 'Brand Form',
                    'brand_name' => 'Brand Name',
                    'website' => 'Website',
                    'logo' => 'Logo',
                ],

                'view' => 'View Brand',
            ],

            'locations' => [
                'provinces' => [
                    'add_province' => 'Add Province',
                    'has_been_created' => ' has been created as a new Province',
                    'province_name' => 'Province Name',
                    'full_name' => 'Full Name',
                    'code' => 'Code',
                    'has_city' => 'Has City',
                ],
                'cities' => [
                    'add_city' => 'Add City',
                    'has_been_created' => ' has been created as a new City',
                    'city_name' => 'City Name',
                    'full_name' => 'Full Name',
                    'code' => 'Code',
                ]
            ],

            'workspace' => [
                'management' => 'Workspaces',
            ],

            'service_type' => [
                'management' => 'Service Type Management',
            ],

            'workforce_management' => [
                'management' => 'Workforce Management',
                'request_type' => 'Request Type',
                'reasons' => 'Reasons',
                'color_coding' => 'Color Coding',
                'approval_settings' => 'Approval Settings',
                'default_groups' => 'Default Groups',
                'calendar_notes' => 'Calendar Notes',
                'calendar_notes_type' => 'Calendar Notes Type'

            ],

            'geolocalizations' => [
                'management' => 'Geolocalized Pages',
            ],

            'algolia_permission' => [
                'management' => 'Algolia Permission Management',
            ],

            'algolia_class' => [
                'management' => 'Algolia Class Management',
            ],

            'provider_type' => [
                'management' => 'Provider Type Management',
            ],

            'physical_code_type' => [
                'management' => 'Physical Code Type Management',
            ],

            'information_type' => [
                'management' => 'Information Type Management',
            ],

            'services' => [
                'management' => 'Services Management',
            ],

            'actors' => [
                'management' => 'Actors Management',
            ],

            'services_exclusive' => [
                'management' => 'Services Exclusive Management',
            ],

            'statistics' => [
                'management' => 'Statistics',
            ],

            'providers' => [
                'management' => 'Providers Management',
            ],

            'parameters' => [
                'management' => 'Parameters Management',
            ],

            'provider_services' => [
                'management' => 'Provider Services Management',
            ],

            'provider_groups' => [
                'management' => 'Provider Groups Management',
            ],


            'profiles' => [
                'active' => 'Client Profiles',
                'create' => 'Create Client Profile',
                'deactivated' => 'Deactivated Profiles',
                'deleted' => 'Deleted Profiles',
                'edit' => 'Edit Profile',
                'management' => 'Client Profile Management',
                'no_permissions' => 'No Permissions',
                'no_roles' => 'No Roles to set.',
                'permissions' => 'Permissions',
                'contact_type_actions' => 'Client Profile Actions',
                'view' => 'View Client Profile',
                'client_profile' => 'Client Profile',
                'table' => [
                    'brand' => 'Brand',
                    'confirmed' => 'Confirmed',
                    'created' => 'Created',
                    'id' => 'ID',
                    'last_updated' => 'Last Updated',
                    'full_name' => 'Full Name',
                    'nickname' => 'Nickname',
                    'for_what_brand' => 'For what brand?',
                    'primary_email' => 'Primary Email',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Overview',
                        'history' => 'History',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Confirmed',
                            'created_at' => 'Created At',
                            'deleted_at' => 'Deleted At',
                            'email' => 'E-mail',
                            'last_login_at' => 'Last Login At',
                            'last_login_ip' => 'Last Login IP',
                            'last_updated' => 'Last Updated',
                            'name' => 'Name',
                            'first_name' => 'First Name',
                            'last_name' => 'Last Name',
                            'status' => 'Status',
                            'timezone' => 'Timezone',
                        ],
                    ],
                ],

                

                'forms' => [
                    'add_title' => 'Add new client for ',
                    'edit_title' => 'Edit Profile ',
                    'add_contacts' => 'Add contacts for ',
                    'add_notes' => 'Add notes for ',
                    'title' => 'Client Profile Form',
                    'surname' => 'Surname',
                    'firstname' => 'First Name',
                    'middlename' => 'Middle Name',
                    'nickname' => 'Nickname',
                    'primary_email' => 'Primary Email',
                    'add_notes_now' => 'Add Notes Now',
                    'date_requested' => 'Interaction Date',
                    'notes_added_by' => 'Notes added by',
                    'notes' => 'Notes',
                    'contacts' => 'Contacts',
                    'jobs' => 'Jobs',
                    'location' => 'Location',
                    'country' => 'Country',
                    'province' => 'Province/Division',
                    'city' => 'City',
                    'add_notes_to' => 'Add notes to',
                    'add_contacts_to' => 'Add contacts to',
                    'note_form' => 'Notes Form',
                    'add_notes_to' => 'Add Notes Now',
                    'add_contacts_now' => 'Add Contacts Now',
                    'add_jobs_now' => 'Add Jobs Now',
                    'add_client_engagements_now' => 'Add Client Engagements Now',
                    'contact_type' => "Contact Type",
                    'contact_information' => "Contact Details",
                    'select_contact_type' => "Select Contact Type",
                    'create_new_contact_type' => "Add new contact type",
                    'missing_surname' => "Surname required",
                    'missing_firstname' => "First name required",
                    'existing_notes' => 'Existing notes',
                    'new_notes' => 'New notes',
                    'brands' => 'Brands',
                    'select_brand' => 'Select brand(s)',
                    'added_note' => 'New note has been added to ',
                    'added_contact' => 'New contact has been added to ',
                    'add_new_province' => 'Add New Province',
                    'add_new_city' => 'Add New City',
                    'add_job' => 'Add Job',
                    'add_client_engagement' => 'Add Client Engagement',
                    'add_contact' => 'Add Contact',
                    'add_note' => 'Add Note',

                    'see_existing_notes' =>  'See existing notes',
                    'see_existing_contacts' =>  'See existing contacts',

                    'new' =>  'New',
                    
                    'checking_database_for_matches' =>  'Checking database for matches',
                    'warning_profile_with_same_name' =>  'WARNING: Found profile with same name, primary email for this profile is',
                    'no_profile_with_same_name' =>  'No profile found with the same name',

                    'warning_profile_with_same_primary_email' =>  'ERROR: Found profile with same primary email, client\'s full name is',
                    'no_profile_with_same_primary_email' =>  'No profile found with the primary email',
                    
                    'job_category' =>  'Job Category',
                    'new_category' =>  'New Category',
                    'profession' =>  'Profession',
                    'new_profession' =>  'New Profession',
                    'description' =>  'Specialization/Position',
                    'new_description' =>  'New Description',

                    'engagements' => 'Engagements'
                ],

                'view_profile' => [
                    'full_name' => 'Full Name',
                    'nickname' => 'Nickname',
                    'primary_email' => 'Primary Email',
                    'location' => 'Location',
                    'profession' => 'Profession',
                    'edit_profile' => 'Edit Profile',
                    'contacts' => 'Contacts',
                    'contact_type' => 'Contact Type',
                    'contact_information' => 'Contact Details',
                    'no_contacts' => 'No contacts found',
                    'date_requested' => 'Interaction Date',
                    'notes' => 'Notes',
                    'note_form' => 'Note Form',
                    'add_note' => 'Add Notes',
                    'edit_note' => 'Edit Note',
                    'no_notes' => 'No notes found',
                    'notes_added_by' => 'Notes added by',
                    'see_clients_list' => 'See clients list',
                    'contact_form' => 'Contact Form',
                    'add_contact' => 'Add Contacts',
                    'edit_contact' => 'Edit Contact',
                    'no_contacts' => 'No contacts found',
                    'contacts_added_by' => 'Contacts added by',
                    'client_engagements' => 'Client Engagements',
                    'add_client_engagements' => 'Add Client Engagements',
                    'no_client_engagements' => 'No client engagements found',
                    'specialization' =>  'Specialization',
                    'jobs' =>  'Jobs',
                ],
                
            ],

            'questions' => [
                'question_types' => [
                    'question_types' => 'Question types'
                ],
                'question_list' => [
                    'question_list' => 'Question List'
                ],
            ],
        ],
    ],

    'frontend' => [
        'auth' => [
            'login_box_title' => 'Login',
            'login_button' => 'Login',
            'login_with' => 'Login with :social_media',
            'register_box_title' => 'Register',
            'register_button' => 'Register',
            'remember_me' => 'Remember Me',
        ],

        'contact' => [
            'box_title' => 'Contact Us',
            'button' => 'Send Information',
        ],

        'passwords' => [
            'expired_password_box_title' => 'Your password has expired.',
            'forgot_password' => 'Forgot Your Password?',
            'reset_password_box_title' => 'Reset Password',
            'reset_password_button' => 'Reset Password',
            'update_password_button' => 'Update Password',
            'send_password_reset_link_button' => 'Send Password Reset Link',
        ],

        'user' => [
            'passwords' => [
                'change' => 'Change Password',
            ],

            'profile' => [
                'avatar' => 'Avatar',
                'created_at' => 'Created At',
                'edit_information' => 'Edit Information',
                'email' => 'E-mail',
                'last_updated' => 'Last Updated',
                'name' => 'Name',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'update_information' => 'Update Information',
            ],
        ],
    ],
];
