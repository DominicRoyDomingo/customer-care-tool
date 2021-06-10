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
        'all' => 'Tanan',
        'yes' => 'Oo',
        'no' => 'Dili',
        'copyright' => 'Katungod sa copyright',
        'custom' => 'Pasadya',
        'actions' => 'Mga lihok',
        'active' => 'Aktibo',
        'buttons' => [
            'save' => 'Pagtipig',
            'update' => 'Pagbag-o',
        ],
        'hide' => 'Tagoa',
        'inactive' => 'Dili aktibo',
        'none' => 'Wala',
        'show' => 'Ipakita',
        'toggle_navigation' => 'Toggle Navigation',
        'create_new' => 'Paghimo Bag-o',
        'toolbar_btn_groups' => 'Toolbar nga adunay mga grupo sa butones',
        'more' => 'Daghan pa',
    ],

    'backend' => [
        'access' => [
            'roles' => [
                'platform' => 'Pagdumala sa Platform',
                'tags' => 'Pagdumala sa Tags',
                'brand' => 'Pagdumala sa Mga Tatak',
                'dates' => 'Pagdumala sa Mga Status sa Petsa',
                'content' => 'Pagdumala sa Sulud',
                'category' => 'Pagdumala sa kategorya',
                'items' => 'Pagdumala sa Mga Butang',
                'workspace' => 'Mga lugar-trabahoan',
                'publishing' => [
                    'publishing' => "Pagdumala sa Pagmantala",
                    'type'       => "Matang sa Butang",
                    'item'       => "Mga Item sa Pagmantala"
                ],
                'campaign' => "Pagdumala sa Kampanya",
                'create' => 'Paghimo Papel',
                'edit' => 'Pag-edit sa Papel',
                'management' => 'Pagdumala sa Katungdanan',
                'table' => [
                    'number_of_users' => 'Gidaghan sa mga Ninggamit',
                    'permissions' => 'Mga Pagtugot',
                    'role' => 'Papel',
                    'sort' => 'Pag-ayo',
                    'total' => 'tibuuk nga papel|tibuuk nga tahas',
                ],
            ],

            'users' => [
                'active' => 'Mga Aktibo nga Naggamit',
                'all_permissions' => 'Tanan nga mga Pagtugot',
                'change_password' => 'Pagbag-o sa Password',
                'change_password_for' => 'Pagbag-o sa Password alang sa :user',
                'create' => 'Paghimo Gumamit',
                'deactivated' => 'Gi-deactivate ang mga Gumagamit',
                'deleted' => 'Gitangtang ang mga Gumagamit',
                'edit' => 'Pag-edit sa Gumagamit',
                'management' => 'Pagdumala sa Team',
                'no_permissions' => 'Wala’y mga Pagtugot',
                'no_roles' => 'Wala’y Mga Katungdanan nga itakda.',
                'permissions' => 'Mga Pagtugot',
                'user_actions' => 'Mga Lihok sa Gumagamit',
                'invite' => 'Pagdapit Mga Gumagamit',

                'table' => [
                    'confirmed' => 'Gikumpirma',
                    'created' => 'Gibuhat',
                    'email' => 'E-mail',
                    'id' => 'ID',
                    'last_updated' => 'Katapusan nga gi-update',
                    'name' => 'Ngalan',
                    'first_name' => 'Unang Ngalan',
                    'last_name' => 'Katapusan nga Ngalan',
                    'no_deactivated' => 'Wala Gumamit mga Na-deactivate',
                    'no_deleted' => 'Wala Tapusanga nga mga Gumagamit',
                    'other_permissions' => 'Uban pang mga Permiso',
                    'permissions' => 'Mga Pagtugot',
                    'abilities' => 'Mga abilidad',
                    'roles' => 'Mga Papel',
                    'social' => 'Sosyal',
                    'total' => 'tibuuk nga tiggamit|tibuuk nga mga ninggamit',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Paghinuktok',
                        'history' => 'Kasaysayan',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Confirmed',
                            'created_at' => 'Gihimo Atong',
                            'deleted_at' => 'Gipapas Atong',
                            'email' => 'E-mail',
                            'last_login_at' => 'Last Login Atong',
                            'last_login_ip' => 'Last Login IP',
                            'last_updated' => 'Katapusan nga gi-update',
                            'name' => 'Ngalan',
                            'first_name' => 'Unang Ngalan',
                            'last_name' => 'Katapusan nga Ngalan',
                            'status' => 'Kahimtang',
                            'timezone' => 'Timezone',
                        ],
                    ],
                ],

                'view' => 'View User',
            ],

            'contact_types' => [
                'active' => 'Mga Matang sa Pagkontak',
                'create' => 'Paghimo Type sa Kontaka',
                'deactivated' => 'Na-deactivate nga Mga Matang sa Pagkontak',
                'deleted' => 'Gitangtang ang Mga Matang sa Pagkontak',
                'edit' => 'Pag-edit sa Tipo sa Pagkontak',
                'management' => 'Pagdumala sa Type Type',
                'no_permissions' => 'Wala’y mga Pagtugot',
                'no_roles' => 'Wala’y Mga Katungdanan nga itakda.',
                'permissions' => 'Mga Pagtugot',
                'contact_type_actions' => 'Contact Type Actions',

                'table' => [
                    'confirmed' => 'Gikumpirma',
                    'created' => 'Gibuhat',
                    'id' => 'ID',
                    'last_updated' => 'Katapusan nga gi-update',
                    'type_name' => 'Type Ngalan',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Paghinuktok',
                        'history' => 'Kasaysayan',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Gikumpirma',
                            'created_at' => 'Gihimo Atong',
                            'updated_at' => 'Giusab Atong',
                            'email' => 'E-mail',
                            'last_login_at' => 'Last Login Atong',
                            'last_login_ip' => 'Last Login IP',
                            'last_updated' => 'Last Updated',
                            'name' => 'Ngalan',
                            'first_name' => 'Unang Ngalan',
                            'last_name' => 'Katapusan nga Ngalan',
                            'status' => 'Kahimtang',
                            'timezone' => 'Timezone',
                        ],
                    ],
                ],

                'forms' => [
                    'title' => 'Ngalan sa Tipo sa Pakigsulti',
                    'type_name' => 'Type Ngalan',
                ],

                'view' => 'Tan-awa ang Matang sa Pagkontak',
            ],

            'brands' => [
                'active' => 'Mga tatak',
                'create' => 'Paghimo Brand',
                'deactivated' => 'Gi-deactivate nga Mga Tatak',
                'deleted' => 'Gitangtang ang Mga Tatak',
                'edit' => 'Pag-usab sa Brand',
                'management' => 'Pagdumala sa Brand',
                'no_permissions' => 'Wala’y mga Pagtugot',
                'no_roles' => 'Wala’y Mga Katungdanan nga itakda.',
                'permissions' => 'Mga Pagtugot',
                'brand_actions' => 'Mga Lihok sa Brand',

                'table' => [
                    'confirmed' => 'Gikumpirma',
                    'id' => 'ID',
                    'created_at' => 'Gihimo Atong',
                    'updated_at' => 'Giusab Atong',
                    'logo' => 'Logo',
                    'brand_name' => 'Ngalan sa Brand',
                    'website' => 'Website',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Overview',
                        'history' => 'Kasaysayan',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Gikumpirma',
                            'created_at' => 'Gihimo Atong',
                            'deleted_at' => 'Gipapas Atong',
                            'email' => 'E-mail',
                            'last_login_at' => 'Last Login Atong',
                            'last_login_ip' => 'Last Login IP',
                            'last_updated' => 'Katapusan nga gi-update',
                            'name' => 'Ngalan',
                            'first_name' => 'Unang Ngalan',
                            'last_name' => 'Katapusan nga Ngalan',
                            'status' => 'Kahimtang',
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
                'management' => 'Mga Panid sa Geolocalized',
            ],

            'algolia_permission' => [
                'management' => 'Pagdumala sa Pagtugot sa Algolia',
            ],

            'algolia_class' => [
                'management' => 'Pagdumala sa Klase sa Algolia',
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
                    'confirmed' => 'Gikumpirma',
                    'created' => 'Gibuhat',
                    'id' => 'ID',
                    'last_updated' => 'Katapusan nga gi-update',
                    'full_name' => 'Hingpit nga Ngalan',
                    'nickname' => 'Angga',
                    'for_what_brand' => 'Alang sa unsang brand?',
                    'primary_email' => 'Panguna nga Email',
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
