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
        'all' => 'Lahat',
        'yes' => 'Oo',
        'no' => 'Hindi',
        'copyright' => 'Copyright',
        'custom' => 'Pasadya',
        'actions' => 'Mga kilos',
        'active' => 'Aktibo',
        'buttons' => [
            'save' => 'Magtipid',
            'update' => 'Update',
        ],
        'hide' => 'Tago',
        'inactive' => 'Hindi aktibo',
        'none' => 'Wala',
        'show' => 'Ipakita',
        'toggle_navigation' => 'Toggle Navigation',
        'create_new' => 'Gumawa ng bago',
        'toolbar_btn_groups' => 'Toolbar na may mga pangkat ng pindutan',
        'more' => 'Dagdag pa',
    ],

    'backend' => [
        'access' => [
            'roles' => [
                'platform' => 'Pamamahala sa Platform',
                'tags' => 'Pamamahala sa Mga Tags',
                'brand' => 'Pamamahala ng Mga Tatak',
                'dates' => 'Pamamahala ng Mga Status ng Petsa',
                'content' => 'Pamamahala sa Nilalaman',
                'category' => 'Pamamahala ng kategorya',
                'items' => 'Pamamahala ng Item',
                'workspace' => 'Mga workspace',
                'publishing' => [
                    'publishing' => "Pamamahala sa Pag-publish",
                    'type'       => "Uri ng Item",
                    'item'       => "Mga Item sa Pag-publish"
                ],
                'campaign' => "Pamamahala sa Kampanya",
                'create' => 'Lumikha ng Papel',
                'edit' => 'Tungkulin sa Pag-edit',
                'management' => 'Pangangasiwa ng Papel',
                'table' => [
                    'number_of_users' => 'Bilang ng mga gumagamit',
                    'permissions' => 'Mga Pahintulot',
                    'role' => 'Papel',
                    'sort' => 'Pagbukud-bukurin',
                    'total' => 'kabuuan ng tungkulin | kabuuang tungkulin',
                ],
            ],

            'users' => [
                'active' => 'Mga Aktibong Gumagamit',
                'all_permissions' => 'Lahat ng Pahintulot',
                'change_password' => 'Palitan ang password',
                'change_password_for' => 'Palitan ang Password para sa :user',
                'create' => 'Lumikha ng Gumagamit',
                'deactivated' => 'Na-deactivate ang Mga Gumagamit',
                'deleted' => 'Mga Tinanggal na Gumagamit',
                'edit' => 'I-edit ang Gumagamit',
                'management' => 'Pamamahala ng Koponan',
                'no_permissions' => 'Walang Pahintulot',
                'no_roles' => 'Walang Itinatakda na Mga Tungkulin.',
                'permissions' => 'Mga Pahintulot',
                'user_actions' => 'Mga Pagkilos ng Gumagamit',
                'invite' => 'Mag-imbita ng Mga Gumagamit',

                'table' => [
                    'confirmed' => 'Nakumpirma',
                    'created' => 'Nilikha',
                    'email' => 'E-mail',
                    'id' => 'ID',
                    'last_updated' => 'Huling nai-update',
                    'name' => 'Pangalan',
                    'first_name' => 'Pangalan',
                    'last_name' => 'Huling pangalan',
                    'no_deactivated' => 'Walang Mga Na-deactivate na User',
                    'no_deleted' => 'Walang Mga Tinanggal na Gumagamit',
                    'other_permissions' => 'Iba Pang Mga Pahintulot',
                    'permissions' => 'Mga Pahintulot',
                    'abilities' => 'Kakayahan',
                    'roles' => 'Mga Tungkulin',
                    'social' => 'Panlipunan',
                    'total' => 'kabuuang gumagamit | kabuuang mga gumagamit',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Pangkalahatang-ideya',
                        'history' => 'Kasaysayan',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Nakumpirma',
                            'created_at' => 'Nilikha At',
                            'deleted_at' => 'Tinanggal At',
                            'email' => 'E-mail',
                            'last_login_at' => 'Huling Pag-login Sa',
                            'last_login_ip' => 'Huling Pag-login IP',
                            'last_updated' => 'Huling nai-update',
                            'name' => 'Pangalan',
                            'first_name' => 'Pangalan',
                            'last_name' => 'Huling pangalan',
                            'status' => 'Katayuan',
                            'timezone' => 'Timezone',
                        ],
                    ],
                ],

                'view' => 'View User',
            ],

            'contact_types' => [
                'active' => 'Mga Uri ng Pakikipag-ugnay',
                'create' => 'Lumikha ng Uri ng Pakikipag-ugnay',
                'deactivated' => 'Na-deactivate na Mga Uri ng Pakikipag-ugnay',
                'deleted' => 'Tinanggal ang Mga Uri ng Pakikipag-ugnay',
                'edit' => 'I-edit ang Uri ng Pakikipag-ugnay',
                'management' => 'Pamamahala ng Uri ng contact',
                'no_permissions' => 'Walang Pahintulot',
                'no_roles' => 'Walang Itinatakda na Mga Tungkulin.',
                'permissions' => 'Mga Pahintulot',
                'contact_type_actions' => 'Mga Pagkilos na Uri ng Pakikipag-ugnay',

                'table' => [
                    'confirmed' => 'Nakumpirma',
                    'created' => 'Nilikha',
                    'id' => 'ID',
                    'last_updated' => 'Huling nai-update',
                    'type_name' => 'Pangalan ng Uri',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Pangkalahatang-ideya',
                        'history' => 'Kasaysayan',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Nakumpirma',
                            'created_at' => 'Nilikha At',
                            'deleted_at' => 'Tinanggal At',
                            'email' => 'E-mail',
                            'last_login_at' => 'Huling Pag-login Sa',
                            'last_login_ip' => 'Huling Pag-login IP',
                            'last_updated' => 'Huling nai-update',
                            'name' => 'Pangalan',
                            'first_name' => 'Pangalan',
                            'last_name' => 'Huling pangalan',
                            'status' => 'Katayuan',
                            'timezone' => 'Timezone',
                        ],
                    ],
                ],

                'forms' => [
                    'title' => 'Pangalan ng Uri ng Pakikipag-ugnay',
                    'type_name' => 'Pangalan ng Uri',
                ],

                'view' => 'Tingnan ang Uri ng Pakikipag-ugnay',
            ],

            'brands' => [
                'active' => 'Mga tatak',
                'create' => 'Lumikha ng Brand',
                'deactivated' => 'Na-deactivate na Mga Tatak',
                'deleted' => 'Tinanggal ang Brands',
                'edit' => 'I-edit ang Brand',
                'management' => 'Pamamahala ng Brand',
                'no_permissions' => 'Walang Pahintulot',
                'no_roles' => 'Walang Itinatakda na Mga Tungkulin.',
                'permissions' => 'Permissions',
                'brand_actions' => 'Mga Pagkilos ng Brand',

                'table' => [
                    'confirmed' => 'Nakumpirma',
                    'id' => 'ID',
                    'created_at' => 'Nilikha At',
                    'updated_at' => 'Nai-update Sa',
                    'logo' => 'Logo',
                    'brand_name' => 'Tatak',
                    'website' => 'Website',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Pangkalahatang-ideya',
                        'history' => 'Kasaysayan',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Nakumpirma',
                            'created_at' => 'Nilikha At',
                            'deleted_at' => 'Tinanggal At',
                            'email' => 'E-mail',
                            'last_login_at' => 'Huling Pag-login Sa',
                            'last_login_ip' => 'Huling Pag-login IP',
                            'last_updated' => 'Huling nai-update',
                            'name' => 'Pangalan',
                            'first_name' => 'Pangalan',
                            'last_name' => 'Huling pangalan',
                            'status' => 'Katayuan',
                            'timezone' => 'Timezone',
                        ],
                    ],
                ],

                'forms' => [
                    'title' => 'Form ng Brand',
                    'brand_name' => 'Tatak',
                    'website' => 'Website',
                    'logo' => 'Logo',
                ],

                'view' => 'Tingnan ang Brand',
            ],

            'locations' => [
                'provinces' => [
                    'add_province' => 'Idagdag ang Lalawigan',
                    'has_been_created' => ' ay nilikha bilang isang bagong Lalawigan',
                    'province_name' => 'Pangalan ng Lalawigan',
                    'full_name' => 'Buong pangalan',
                    'code' => 'Code',
                    'has_city' => 'May Lungsod',
                ],
                'cities' => [
                    'add_city' => 'Magdagdag ng Lungsod',
                    'has_been_created' => ' ay nilikha bilang isang bagong Lungsod',
                    'city_name' => 'pangalan ng lungsod',
                    'full_name' => 'Buong pangalan',
                    'code' => 'Code',
                ]
            ],

            'workspace' => [
                'management' => 'Mga workspace',
            ],

            'service_type' => [
                'management' => 'Pamamahala ng Uri ng Serbisyo',
            ],

            'workforce_management' => [
                'management' => 'Pamamahala sa Trabaho',
                'request_type' => 'Uri ng Kahilingan',
                'reasons' => 'Mga Dahilan',
                'color_coding' => 'Pag-coding ng Kulay',
                'approval_settings' => 'Mga Setting ng Pag-apruba',
                'default_groups' => 'Mga Default na Grupo',
                'calendar_notes' => 'Mga Tala sa Kalendaryo',
                'calendar_notes_type' => 'Uri ng Mga Tala sa Kalendaryo'

            ],

            'geolocalizations' => [
                'management' => 'Mga Geolocalized na Pahina',
            ],

            'algolia_permission' => [
                'management' => 'Pamamahala sa Pahintulot ng Algolia',
            ],

            'algolia_class' => [
                'management' => 'Pamamahala sa Klase ng Algolia',
            ],

            'provider_type' => [
                'management' => 'Pamamahala ng Uri ng Provider',
            ],

            'physical_code_type' => [
                'management' => 'Pamamahala ng Uri ng Physical Code',
            ],

            'information_type' => [
                'management' => 'Pamamahala ng Uri ng Impormasyon',
            ],

            'services' => [
                'management' => 'Pamamahala ng Mga Serbisyo',
            ],

            'actors' => [
                'management' => 'Pamamahala ng Mga Aktor',
            ],

            'services_exclusive' => [
                'management' => 'Serbisyong Eksklusibo sa Pamamahala',
            ],

            'statistics' => [
                'management' => 'Mga Istatistika',
            ],

            'providers' => [
                'management' => 'Pamamahala ng Mga Tagabigay',
            ],

            'parameters' => [
                'management' => 'Pamamahala ng Parameter',
            ],

            'provider_services' => [
                'management' => 'Provider Services Management',
            ],

            'provider_groups' => [
                'management' => 'Pamamahala ng Mga Grupo ng Provider',
            ],


            'profiles' => [
                'active' => 'Mga Profile ng kliyente',
                'create' => 'Lumikha ng Client Profile',
                'deactivated' => 'Na-deactivate na Mga Profile',
                'deleted' => 'Tinanggal ang Mga Profile',
                'edit' => 'Ibahin ang profile',
                'management' => 'Pamamahala sa Profile ng Client',
                'no_permissions' => 'Walang Pahintulot',
                'no_roles' => 'Walang Itinatakda na Mga Tungkulin.',
                'permissions' => 'Mga Pahintulot',
                'contact_type_actions' => 'Mga Pagkilos ng Profile ng Client',
                'view' => 'Tingnan ang Profile ng Client',
                'client_profile' => 'Profile ng Client',
                'table' => [
                    'brand' => 'Tatak',
                    'confirmed' => 'Nakumpirma',
                    'created' => 'Nilikha',
                    'id' => 'ID',
                    'last_updated' => 'Huling nai-update',
                    'full_name' => 'Buong pangalan',
                    'nickname' => 'Palayaw',
                    'for_what_brand' => 'Para sa anong tatak?',
                    'primary_email' => 'Pangunahing Email',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Pangkalahatang-ideya',
                        'history' => 'Kasaysayan',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Nakumpirma',
                            'created_at' => 'Nilikha At',
                            'deleted_at' => 'Tinanggal At',
                            'email' => 'E-mail',
                            'last_login_at' => 'Huling Pag-login Sa',
                            'last_login_ip' => 'Huling Pag-login IP',
                            'last_updated' => 'Huling nai-update',
                            'name' => 'Pangalan',
                            'first_name' => 'Pangalan',
                            'last_name' => 'Huling pangalan',
                            'status' => 'Katayuan',
                            'timezone' => 'Timezone',
                        ],
                    ],
                ],

                

                'forms' => [
                    'add_title' => 'Magdagdag ng bagong client para sa ',
                    'edit_title' => 'Ibahin ang profile ',
                    'add_contacts' => 'Magdagdag ng mga contact para sa ',
                    'add_notes' => 'Magdagdag ng mga tala para sa ',
                    'title' => 'Form ng Profile ng Client',
                    'surname' => 'Apelyido',
                    'firstname' => 'Pangalan',
                    'middlename' => 'Gitnang pangalan',
                    'nickname' => 'Palayaw',
                    'primary_email' => 'Pangunahing Email',
                    'add_notes_now' => 'Magdagdag ng Tala Ngayon',
                    'date_requested' => 'Petsa ng Pakikipag-ugnay',
                    'notes_added_by' => 'Mga tala na idinagdag ni',
                    'notes' => 'Mga tala',
                    'contacts' => 'Mga contact',
                    'jobs' => 'Mga trabaho',
                    'location' => 'Lokasyon',
                    'country' => 'Bansa',
                    'province' => 'Lalawigan / Dibisyon',
                    'city' => 'Lungsod',
                    'add_notes_to' => 'Magdagdag ng mga tala sa',
                    'add_contacts_to' => 'Magdagdag ng mga contact sa',
                    'note_form' => 'Mga Pormang Tala',
                    'add_notes_to' => 'Magdagdag ng tala Ngayon',
                    'add_contacts_now' => 'Magdagdag ng mga contact Ngayon',
                    'add_jobs_now' => 'Magdagdag ng trabaho Ngayon',
                    'add_client_engagements_now' => 'Magdagdag ng Mga Pakikipag-ugnay sa Client Ngayon',
                    'contact_type' => "Uri ng Pakikipag-ugnay",
                    'contact_information' => "Mga Detalye ng Pakikipag-ugnay",
                    'select_contact_type' => "Piliin ang Uri ng Pakikipag-ugnay",
                    'create_new_contact_type' => "Magdagdag ng bagong uri ng contact",
                    'missing_surname' => "Kinakailangan ang apelyido",
                    'missing_firstname' => "Kailangan ng unang pangalan",
                    'existing_notes' => 'Umiiral na tala',
                    'new_notes' => 'Mga bagong tala',
                    'brands' => 'Mga tatak',
                    'select_brand' => 'Piliin ang (mga) tatak',
                    'added_note' => 'Ang bagong tala ay naidagdag sa ',
                    'added_contact' => 'Nagdagdag ng bagong contact sa ',
                    'add_new_province' => 'Magdagdag ng Bagong Lalawigan',
                    'add_new_city' => 'Magdagdag ng Bagong Lungsod',
                    'add_job' => 'Idagdag si Job',
                    'add_client_engagement' => 'Magdagdag ng Pakikipag-ugnay sa Client',
                    'add_contact' => 'Magdagdag ng Makipag-ugnay',
                    'add_note' => 'Magdagdag ng tala',

                    'see_existing_notes' =>  'Tingnan ang mayroon nang mga tala',
                    'see_existing_contacts' =>  'Tingnan ang mga mayroon nang contact',

                    'new' =>  'Bago',
                    
                    'checking_database_for_matches' =>  'Sinusuri ang database para sa mga tugma',
                    'warning_profile_with_same_name' =>  'BABALA: Natagpuan ang profile na may parehong pangalan, pangunahing email para sa profile na ito ay',
                    'no_profile_with_same_name' =>  'Walang nahanap na profile na may parehong pangalan',

                    'warning_profile_with_same_primary_email' =>  'ERROR: Natagpuan ang profile na may parehong pangunahing email, ang buong pangalan ng client ay',
                    'no_profile_with_same_primary_email' =>  'Walang natagpuang profile kasama ang pangunahing email',
                    
                    'job_category' =>  'Kategoryang Trabaho',
                    'new_category' =>  'Bagong kategorya',
                    'profession' =>  'Propesyon',
                    'new_profession' =>  'Bagong Propesyon',
                    'description' =>  'Pagpapadalubhasa / Posisyon',
                    'new_description' =>  'Bagong Paglalarawan',

                    'engagements' => 'Pakikipag-ugnayan'
                ],

                'view_profile' => [
                    'full_name' => 'Buong pangalan',
                    'nickname' => 'Palayaw',
                    'primary_email' => 'Pangunahing Email',
                    'location' => 'Lokasyon',
                    'profession' => 'Propesyon',
                    'edit_profile' => 'Ibahin ang profile',
                    'contacts' => 'Mga contact',
                    'contact_type' => 'Uri ng Pakikipag-ugnay',
                    'contact_information' => 'Mga Detalye ng Pakikipag-ugnay',
                    'no_contacts' => 'Walang nahanap na mga contact',
                    'date_requested' => 'Petsa ng Pakikipag-ugnay',
                    'notes' => 'Mga tala',
                    'note_form' => 'Porma ng Tala',
                    'add_note' => 'Magdagdag ng Tala',
                    'edit_note' => 'I-edit ang Tandaan',
                    'no_notes' => 'Walang nahanap na tala',
                    'notes_added_by' => 'Mga tala na idinagdag ni',
                    'see_clients_list' => 'Tingnan ang listahan ng mga kliyente',
                    'contact_form' => 'Form sa Pakikipag-ugnay',
                    'add_contact' => 'Magdagdag ng Kontak',
                    'edit_contact' => 'I-edit ang Makipag-ugnay',
                    'no_contacts' => 'Walang nahanap na mga contact',
                    'contacts_added_by' => 'Mga contact na idinagdag ni',
                    'client_engagements' => 'Mga Pakikipag-ugnay sa kliyente',
                    'add_client_engagements' => 'Magdagdag ng Mga Pakikipag-ugnay sa Client',
                    'no_client_engagements' => 'Walang natagpuang mga pakikipag-ugnayan sa kliyente',
                    'specialization' =>  'Pagdadalubhasa',
                    'jobs' =>  'Mga trabaho',
                ],
                
            ],
        ],
    ],

    'frontend' => [
        'auth' => [
            'login_box_title' => 'Mag log in',
            'login_button' => 'Mag log in',
            'login_with' => 'Mag-login kasama si :social_media',
            'register_box_title' => 'Magparehistro',
            'register_button' => 'Magparehistro',
            'remember_me' => 'Tandaan mo ako',
        ],

        'contact' => [
            'box_title' => 'Makipag-ugnayan sa amin',
            'button' => 'Magpadala ng Impormasyon',
        ],

        'passwords' => [
            'expired_password_box_title' => 'Nag-expire na ang iyong password.',
            'forgot_password' => 'Nakalimutan ang Iyong Password?',
            'reset_password_box_title' => 'I-reset ang Password',
            'reset_password_button' => 'I-reset ang Password',
            'update_password_button' => 'I-update ang Password',
            'send_password_reset_link_button' => 'Magpadala ng Link ng Pag-reset ng Password',
        ],

        'user' => [
            'passwords' => [
                'change' => 'Palitan ANG password',
            ],

            'profile' => [
                'avatar' => 'Avatar',
                'created_at' => 'Nilikha At',
                'edit_information' => 'I-edit ang Impormasyon',
                'email' => 'E-mail',
                'last_updated' => 'Huling nai-update',
                'name' => 'Pangalan',
                'first_name' => 'Pangalan',
                'last_name' => 'Huling pangalan',
                'update_information' => 'I-update ang Impormasyon',
            ],
        ],
    ],
];
