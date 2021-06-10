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
        'all' => 'Tutti',
        'yes' => 'Sì',
        'no' => 'No',
        'copyright' => 'Diritto d\'autore',
        'custom' => 'Custom', // TODO TRANSLATION
        'actions' => 'Azioni',
        'active' => 'Active',
        'buttons' => [
            'save' => 'Salva',
            'update' => 'Aggiorna',
        ],
        'hide' => 'Nascondi',
        'inactive' => 'Inactive',
        'none' => 'Nessuno',
        'show' => 'Visualizza',
        'toggle_navigation' => 'Menu Navigazione',
        'create_new' => 'Creare nuovo',
        'toolbar_btn_groups' => 'Barra degli strumenti con gruppi di pulsanti',
        'more' => 'Di Più',
    ],

    'backend' => [
        'access' => [
            'roles' => [
                'platform' => 'Gestione della piattaforma',
                'tags' => 'Gestione dei tag',
                'brand' => 'Gestione dei marchi',
                'dates' => 'Gestione degli stati della data',
                'content' => 'Gestione dei contenuti',
                'publishing' => [
                    'publishing' => "Gestione editoriale",
                    'type'       => "Tipo di elemento",
                    'item'       => "Pubblicazione di articoli"
                ],
                'campaign' => "Gestione della campagna",
                'create' => 'Crea ruolo',
                'edit' => 'Modifica ruolo',
                'management' => 'Gestione della squadra',
                'table' => [
                    'number_of_users' => 'Numero di utenti',
                    'permissions' => 'Permessi',
                    'role' => 'Ruolo',
                    'sort' => 'Ordina',
                    'total' => 'Ruolo|Totale ruoli',
                ],
            ],

            'users' => [
                'active' => 'Utenti attivi',
                'all_permissions' => 'Tutti i permessi',
                'change_password' => 'Cambia password',
                'change_password_for' => 'Cambia password per :user',
                'create' => 'Crea utente',
                'deactivated' => 'Utenti disattivati',
                'deleted' => 'Utenti eliminati',
                'edit' => 'Modifica utente',
                'management' => 'Gestione utente',
                'no_permissions' => 'Nessun permesso',
                'no_roles' => 'Nessuno ruolo da assegnare.',
                'permissions' => 'Permessi',
                'user_actions' => 'Azioni dell\'utente',
                'invite' => 'Invita utenti',

                'table' => [
                    'confirmed' => 'Confermato',
                    'created' => 'Creato',
                    'email' => 'E-mail',
                    'id' => 'ID',
                    'last_updated' => 'Ultimo aggiornamento',
                    'name' => 'Nome',
                    'first_name' => 'Nome di battesimo',
                    'last_name' => 'Cognome',
                    'no_deactivated' => 'Nessun utente disattivato',
                    'no_deleted' => 'Nessun utente eliminato',
                    'other_permissions' => 'Altre autorizzazioni',
                    'permissions' => 'Permessi',
                    'abilities' => 'Abilità',
                    'roles' => 'Ruoli',
                    'social' => 'Social',
                    'total' => 'utente(i) totali', // TODO: pluralization
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
                            'status' => 'Status',
                            'timezone' => 'Timezone',
                        ],
                    ],
                ],

                'view' => 'View User',
            ],

            'contact_types' => [
                'active' => 'Tipi di contatto',
                'create' => 'Crea tipo di contatto',
                'deactivated' => 'Tipi di contatti disattivati',
                'deleted' => 'Tipi di contatti eliminati',
                'edit' => 'Modifica tipo di contatto',
                'management' => 'Gestione del tipo di contatto',
                'no_permissions' => 'Nessuna autorizzazione',
                'no_roles' => 'Nessun ruolo da impostare.',
                'permissions' => 'Permessi',
                'contact_type_actions' => 'Tipo di contatto Azioni',

                'table' => [
                    'confirmed' => 'Confermata',
                    'created' => 'Creata',
                    'id' => 'ID',
                    'last_updated' => 'Ultimo aggiornamento',
                    'type_name' => 'Digitare il nome',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Panoramica',
                        'history' => 'Storia',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Confermato',
                            'created_at' => 'Creato a',
                            'deleted_at' => 'Eliminato alle',
                            'email' => 'E-mail',
                            'last_login_at' => 'Ultimo accesso alle',
                            'last_login_ip' => 'IP dell\'ultimo accesso',
                            'last_updated' => 'Ultimo aggiornamento',
                            'name' => 'Nome',
                            'first_name' => 'Nome di battesimo',
                            'last_name' => 'Cognome',
                            'status' => 'Stato',
                            'timezone' => 'Fuso orario',
                        ],
                    ],
                ],

                'forms' => [
                    'title' => 'Nome del tipo di contatto',
                    'type_name' => 'Digitare il nome',
                ],

                'view' => 'Visualizza tipo di contatto',
            ],

            'brands' => [
                'active' => 'Marche',
                'create' => 'Crea marchio',
                'deactivated' => 'Marchi disattivati',
                'deleted' => 'Marchi eliminati',
                'edit' => 'Modifica marchio',
                'management' => 'Gestione del marchio',
                'no_permissions' => 'Nessuna autorizzazione',
                'no_roles' => 'Nessun ruolo da impostare.',
                'permissions' => 'Permessi',
                'brand_actions' => 'Azioni sul marchio',

                'table' => [
                    'confirmed' => 'Confermato',
                    'id' => 'ID',
                    'created_at' => 'Creato a',
                    'updated_at' => 'Aggiornato alle',
                    'logo' => 'Logo',
                    'brand_name' => 'Marchio',
                    'website' => 'Sito web',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Panoramica',
                        'history' => 'Storia',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Confermato',
                            'created_at' => 'Creato a',
                            'deleted_at' => 'Eliminato alle',
                            'email' => 'E-mail',
                            'last_login_at' => 'Ultimo accesso alle',
                            'last_login_ip' => 'IP dell\'ultimo accesso',
                            'last_updated' => 'Ultimo aggiornamento',
                            'name' => 'Nome',
                            'first_name' => 'Nome di battesimo',
                            'last_name' => 'Cognome',
                            'status' => 'Stato',
                            'timezone' => 'Fuso orario',
                        ],
                    ],
                ],

                'forms' => [
                    'title' => 'Forma del marchio',
                    'brand_name' => 'Marchio',
                    'website' => 'Sito web',
                    'logo' => 'Logo',
                ],

                'view' => 'Visualizza marchio',
            ],

            'locations' => [
                'provinces' => [
                    'add_province' => 'Aggiungi provincia',
                    'has_been_created' => ' è stata creata come una nuova provincia',
                    'province_name' => 'Nome provincia',
                    'full_name' => 'Nome e cognome',
                    'code' => 'Codice',
                    'has_city' => 'Ha città',
                ],
                'cities' => [
                    'add_city' => 'Aggiungi città',
                    'has_been_created' => ' è stata creata come una nuova città',
                    'city_name' => 'nome della città',
                    'full_name' => 'Nome e cognome',
                    'code' => 'Codice',
                ]
            ],

            'service_type' => [
                'management' => 'Gestione del tipo di servizio',
            ],

            'workforce_management' => [
                'management' => 'Gestione della forza lavoro',
                'request_type' => 'Tipo di richiesta',
                'reasons' => 'Motivi',
                'color_coding' => 'Codificazione del colore',
                'approval_settings' => 'Impostazioni di approvazione',
                'default_groups' => 'Gruppi predefiniti',
                'calendar_notes' => 'Note del calendario',
                'calendar_notes_type' => 'Tipo di note del calendario'

            ],

            'geolocalizations' => [
                'management' => 'Pagine geolocalizzate',
            ],

            'algolia_permission' => [
                'management' => 'Gestione delle autorizzazioni in Algolia',
            ],

            'algolia_class' => [
                'management' => 'Gestione classi Algolia',
            ],

            'provider_type' => [
                'management' => 'Gestione del tipo di provider',
            ],

            'physical_code_type' => [
                'management' => 'Gestione del tipo di codice fisico',
            ],

            'information_type' => [
                'management' => 'Gestione dei tipi di informazioni',
            ],
            
            'services' => [
                'management' => 'Gestione dei servizi',
            ],

            'actors' => [
                'management' => 'Gestione degli attori',
            ],

            'services_exclusive' => [
                'management' => 'Servizi di gestione esclusiva',
            ],

            'providers' => [
                'management' => 'Gestione fornitori',
            ],

            'provider_services' => [
                'management' => 'Gestione dei servizi del provider',
            ],


            'profiles' => [
                'active' => 'Profili del cliente',
                'create' => 'Crea profilo cliente',
                'deactivated' => 'Profili disattivati',
                'deleted' => 'Profili eliminati',
                'edit' => 'Modifica Profilo',
                'management' => 'Gestione del profilo del cliente',
                'no_permissions' => 'Nessuna autorizzazione',
                'no_roles' => 'Nessun ruolo da impostare.',
                'permissions' => 'Permessi',
                'contact_type_actions' => 'Azioni del profilo client',
                'view' => 'Visualizza il profilo del cliente',
                'client_profile' => 'Profilo del cliente',
                'table' => [
                    'confirmed' => 'Confermato',
                    'created' => 'Creato',
                    'id' => 'ID',
                    'last_updated' => 'Ultimo aggiornamento',
                    'full_name' => 'Nome e cognome',
                    'nickname' => 'Soprannome',
                    'for_what_brand' => 'Per quale marchio?',
                    'primary_email' => 'E-mail primario',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Panoramica',
                        'history' => 'Storia',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Confermato',
                            'created_at' => 'Creato a',
                            'deleted_at' => 'Eliminato alle',
                            'email' => 'E-mail',
                            'last_login_at' => 'Ultimo accesso alle',
                            'last_login_ip' => 'IP dell\'ultimo accesso',
                            'last_updated' => 'Ultimo aggiornamento',
                            'name' => 'Nome',
                            'first_name' => 'Nome di battesimo',
                            'last_name' => 'Cognome',
                            'status' => 'Stato',
                            'timezone' => 'Fuso orario',
                        ],
                    ],
                ],

                'forms' => [
                    'title' => 'Modulo del profilo cliente',
                    'surname' => 'Cognome',
                    'firstname' => 'Nome di battesimo',
                    'middlename' => 'Secondo nome',
                    'nickname' => 'Soprannome',
                    'primary_email' => 'E-mail primario',
                    'add_notes_now' => 'Aggiungi note ora',
                    'date_requested' => 'Data di interazione',
                    'notes_added_by' => 'Note aggiunte da',
                    'notes' => 'Appunti',
                    'contacts' => 'Contatti',
                    'jobs' => 'Lavori',
                    'location' => 'Posizione',
                    'country' => 'Nazione',
                    'province' => 'Provincia/Divisione',
                    'city' => 'Città',
                    'add_notes_to' => 'Aggiungi note a',
                    'add_contacts_to' => 'Aggiungi contatti a',
                    'note_form' => 'Modulo note',
                    'add_notes_now' => 'Aggiungi note ora',
                    'add_contacts_now' => 'Aggiungi contatti ora',
                    'add_jobs_now' => 'Aggiungi lavori ora',
                    'add_client_engagements_now' => 'Aggiungi subito gli impegni dei clienti',
                    'contact_type' => "Tipo di contatto",
                    'contact_information' => "Dettagli del contatto",
                    'select_contact_type' => "Seleziona il tipo di contatto",
                    'create_new_contact_type' => "Aggiungi nuovo tipo di contatto",
                    'missing_surname' => "Cognome richiesto",
                    'missing_firstname' => "Nome richiesto",
                    'existing_notes' => 'Note esistenti',
                    'new_notes' => 'Nuove note',
                    'brands' => 'Marche',
                    'select_brand' => 'Seleziona marchio / i',
                    'added_note' => 'New note has been added to ',
                    'added_contact' => 'Nuovo contatto è stato aggiunto a ',
                    'add_new_province' => 'Aggiungi nuova provincia',
                    'add_new_city' => 'Aggiungi nuova città',
                    'add_job' => 'Aggiungi lavoro',
                    'add_contact' => 'Aggiungi contatto',
                    'add_note' => 'Aggiungi nota',

                    'see_existing_notes' =>  'Vedi le note esistenti',
                    'see_existing_contacts' =>  'Vedi i contatti esistenti',

                    'New' =>  'Nuovo',
                    
                    'checking_database_for_matches' =>  'Verifica del database per le partite',
                    'warning_profile_with_same_name' =>  'ATTENZIONE: profilo trovato con lo stesso nome, l\'e-mail principale per questo profilo è',
                    'no_profile_with_same_name' =>  'Nessun profilo trovato con lo stesso nome',

                    'warning_profile_with_same_primary_email' =>  'ERROR: Found profile with same primary email, client\'s full name is',
                    'no_profile_with_same_primary_email' =>  'No profile found with the primary email',
                    
                    'job_category' =>  'Categoria di lavoro',
                    'new_category' =>  'Nuova categoria',
                    'profession' =>  'Professione',
                    'new_profession' =>  'Nuova professione',
                    'description' =>  'Descrizione',
                    'new_description' =>  'Nuova descrizione',
                ],

                'view_profile' => [
                    'full_name' => 'Nome e cognome',
                    'nickname' => 'Soprannome',
                    'primary_email' => 'E-mail primario',
                    'location' => 'Posizione',
                    'profession' => 'Professione',
                    'edit_profile' => 'Modifica Profilo',
                    'contacts' => 'Contatti',
                    'contact_type' => 'Tipo di contatto',
                    'contact_information' => 'Dettagli del contatto',
                    'no_contacts' => 'Nessun contatto trovato',
                    'date_requested' => 'Data di interazione',
                    'notes' => 'Appunti',
                    'note_form' => 'Modulo di nota',
                    'add_note' => 'Aggiungi note',
                    'edit_note' => 'Modifica nota',
                    'no_notes' => 'Nessuna nota trovata',
                    'notes_added_by' => 'Note aggiunte da',
                    'see_clients_list' => 'Vedi l\'elenco dei clienti',
                    'contact_form' => 'Modulo di Contatto',
                    'add_contact' => 'Aggiungi contatti',
                    'edit_contact' => 'Modifica il contatto',
                    'no_contacts' => 'Nessun contatto trovato',
                    'contacts_added_by' => 'Contatti aggiunti da',
                    'client_engagements' => 'Coinvolgimento del cliente',
                    'add_client_engagements' => 'Aggiungi coinvolgimento del cliente',
                    'no_client_engagements' => 'Nessun coinvolgimento del cliente trovato',
                    'specialization' =>  'Specializzazione',
                    'jobs' =>  'Lavori',
                ],
                
            ],
        ],
    ],

    'frontend' => [
        'auth' => [
            'login_box_title' => 'Login',
            'login_button' => 'Login',
            'login_with' => 'Login tramite :social_media',
            'register_box_title' => 'Registrazione',
            'register_button' => 'Registrati',
            'remember_me' => 'Ricordami',
        ],

        'contact' => [
            'box_title' => 'Contact Us',
            'button' => 'Send Information',
        ],

        'passwords' => [
            'forgot_password' => 'Password dimenticata?',
            'reset_password_box_title' => 'Reset password',
            'reset_password_button' => 'Reset password',
            'send_password_reset_link_button' => 'Invia link per il reset della password',
        ],

        'user' => [
            'passwords' => [
                'change' => 'Change Password',
            ],

            'profile' => [
                'avatar' => 'Avatar',
                'created_at' => 'Data di creazione',
                'edit_information' => 'Modifica informazioni',
                'email' => 'E-mail',
                'last_updated' => 'Ultimo aggiornamento',
                'name' => 'Nome',
                'update_information' => 'Aggiorna informazioni',
            ],
        ],
    ],
];
