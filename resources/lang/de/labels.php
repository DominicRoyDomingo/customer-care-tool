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
        'all' => 'Alle',
        'yes' => 'Ja',
        'no' => 'Nein',
        'copyright' => 'Urheberrechte ©',
        'custom' => 'Erweitert', // TODO TRANSLATION
        'actions' => 'Aktionen',
        'active' => 'Aktiv',
        'buttons' => [
            'save' => 'Speichern',
            'update' => 'Aktualisieren',
        ],
        'hide' => 'Verstecken',
        'inactive' => 'Inaktiv',
        'none' => 'Keine',
        'show' => 'Anzeigen',
        'toggle_navigation' => 'Navigation umschalten',
        'create_new' => 'Neue erstellen',
        'toolbar_btn_groups' => 'Symbolleiste mit Schaltflächengruppen',
        'more' => 'Mehr',
    ],

    'backend' => [
        'access' => [
            'roles' => [
                'platform' => 'Plattformverwaltung',
                'tags' => 'Tags Management',
                'brand' => 'Markenmanagement',
                'dates' => 'Verwaltung des Datumsstatus',
                'content' => 'Content Management',
                'publishing' => [
                    'publishing' => "Publishing Management",
                    'type'       => "Gegenstandsart",
                    'item'       => "Artikel veröffentlichen"
                ],
                'campaign' => "Kampagnenmanagement",
                'create' => 'Rolle erstellen',
                'edit' => 'Rolle bearbeiten',
                'management' => 'Team Management',
                'table' => [
                    'number_of_users' => 'Anzahl Benutzer',
                    'permissions' => 'Berechtigungen',
                    'role' => 'Rolle',
                    'sort' => 'Sortierung',
                    'total' => 'Rolle|Rollen',
                ],
            ],

            'users' => [
                'active' => 'Aktive Benutzer',
                'all_permissions' => 'Alle Berechtigungen',
                'change_password' => 'Kennwort ändern',
                'change_password_for' => 'Kennwort für :user ändern',
                'create' => 'Benutzer erstellen',
                'deactivated' => 'Deaktivierte Benutzer',
                'deleted' => 'Gelöschte Benutzer',
                'edit' => 'Benutzer bearbeiten',
                'management' => 'Benutzer verwalten',
                'no_permissions' => 'Keine Berechtigungen',
                'no_roles' => 'Keine Rollen vorhanden.',
                'permissions' => 'Berechtigungen',
                'user_actions' => 'Benutzeraktionen',
                'invite' => 'Benutzer einladen',

                'table' => [
                    'confirmed' => 'Bestätigt',
                    'created' => 'Erstellt',
                    'email' => 'E-Mail',
                    'id' => 'ID',
                    'last_updated' => 'Letzte Aktualisierung',
                    'name' => 'Name',
                    'first_name' => 'Vorname',
                    'last_name' => 'Nachname',
                    'no_deactivated' => 'Keine deaktivierten Benutzer',
                    'no_deleted' => 'Keine gelöschten Benutzer',
                    'other_permissions' => 'Andere Berechtigungen',
                    'permissions' => 'Berechtigungen',
                    'abilities' => 'Fähigkeiten',
                    'roles' => 'Rollen',
                    'social' => 'Social',
                    'total' => 'Benutzer|Benutzer',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Übersicht',
                        'history' => 'Verlauf',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Avatar',
                            'confirmed' => 'Bestätigt',
                            'created_at' => 'Erstellt am',
                            'deleted_at' => 'Gelöscht am',
                            'email' => 'E-mail',
                            'last_login_at' => 'Last Login At',
                            'last_login_ip' => 'Last Login IP',
                            'last_updated' => 'Zuletzt aktualisiert',
                            'name' => 'Name',
                            'first_name' => 'Vorname',
                            'last_name' => 'Nachname',
                            'status' => 'Status',
                            'timezone' => 'Timezone',
                        ],
                    ],
                ],

                'view' => 'Benutzer anzeigen',
            ],

            'contact_types' => [
                'active' => 'Kontakttypen',
                'create' => 'Kontakttyp erstellen',
                'deactivated' => 'Deaktivierte Kontakttypen',
                'deleted' => 'Gelöschte Kontakttypen',
                'edit' => 'Kontakttyp bearbeiten',
                'management' => 'Kontakttypverwaltung',
                'no_permissions' => 'Keine Berechtigungen',
                'no_roles' => 'Keine zu setzenden Rollen.',
                'permissions' => 'Berechtigungen',
                'contact_type_actions' => 'Kontakttypaktionen',

                'table' => [
                    'confirmed' => 'Bestätigt',
                    'created' => 'Erstellt',
                    'id' => 'ID',
                    'last_updated' => 'Letzte Aktualisierung',
                    'type_name' => 'Modellname',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Überblick',
                        'history' => 'Geschichte',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Benutzerbild',
                            'confirmed' => 'Bestätigt',
                            'created_at' => 'Hergestellt in',
                            'deleted_at' => 'Gelöscht bei',
                            'email' => 'Email',
                            'last_login_at' => 'Letzter Login um',
                            'last_login_ip' => 'Letzte Login IP',
                            'last_updated' => 'Letzte Aktualisierung',
                            'name' => 'Name',
                            'first_name' => 'Vorname',
                            'last_name' => 'Nachname',
                            'status' => 'Status',
                            'timezone' => 'Zeitzone',
                        ],
                    ],
                ],

                'forms' => [
                    'title' => 'Name des Kontakttyps',
                    'type_name' => 'Modellname',
                ],

                'view' => 'Kontakttyp anzeigen',
            ],

            'brands' => [
                'active' => 'Marken',
                'create' => 'Marke erstellen',
                'deactivated' => 'Deaktivierte Marken',
                'deleted' => 'Gelöschte Marken',
                'edit' => 'Marke bearbeiten',
                'management' => 'Markenführung',
                'no_permissions' => 'Keine Berechtigungen',
                'no_roles' => 'Keine zu setzenden Rollen.',
                'permissions' => 'Berechtigungen',
                'brand_actions' => 'Markenaktionen',

                'table' => [
                    'confirmed' => 'Bestätigt',
                    'id' => 'ID',
                    'created_at' => 'Hergestellt in',
                    'updated_at' => 'Aktualisiert am',
                    'logo' => 'Logo',
                    'brand_name' => 'Markenname',
                    'website' => 'Webseite',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Überblick',
                        'history' => 'Geschichte',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Benutzerbild',
                            'confirmed' => 'Bestätigt',
                            'created_at' => 'Hergestellt in',
                            'deleted_at' => 'Gelöscht bei',
                            'email' => 'Email',
                            'last_login_at' => 'Letzter Login um',
                            'last_login_ip' => 'Letzte Login IP',
                            'last_updated' => 'Letzte Aktualisierung',
                            'name' => 'Name',
                            'first_name' => 'Vorname',
                            'last_name' => 'Nachname',
                            'status' => 'Status',
                            'timezone' => 'Zeitzone',
                        ],
                    ],
                ],

                'forms' => [
                    'title' => 'Markenform',
                    'brand_name' => 'Markenname',
                    'website' => 'Webseite',
                    'logo' => 'Logo',
                ],

                'view' => 'Marke anzeigen',
            ],

            'locations' => [
                'provinces' => [
                    'add_province' => 'Provinz hinzufügen',
                    'has_been_created' => ' wurde als neue Provinz geschaffen',
                    'province_name' => 'Provinzname',
                    'full_name' => 'Vollständiger Name',
                    'code' => 'Code',
                    'has_city' => 'Hat Stadt',
                ],
                'cities' => [
                    'add_city' => 'Stadt hinzufügen',
                    'has_been_created' => ' wurde als neue Stadt erstellt',
                    'city_name' => 'Stadtname',
                    'full_name' => 'Vollständiger Name',
                    'code' => 'Code',
                ]
            ],

            'service_type' => [
                'management' => 'Service Type Management',
            ],

            'workforce_management' => [
                'management' => 'Workforce Management',
                'request_type' => 'Anfragetyp',
                'reasons' => 'Gründe dafür',
                'color_coding' => 'Farbcodierung',
                'approval_settings' => 'Genehmigungseinstellungen',
                'default_groups' => 'Standardgruppen',
                'calendar_notes' => 'Kalendernotizen',
                'calendar_notes_type' => 'Kalender Notizen Typ'

            ],

            'geolocalizations' => [
                'management' => 'Anzahl der Geolokalisationen',
            ],

            'algolia_permission' => [
                'management' => 'Algolia Permission Management',
            ],

            'algolia_class' => [
                'management' => 'Algolia Klassenmanagement',
            ],

            'provider_type' => [
                'management' => 'Platztypverwaltung',
            ],

            'physical_code_type' => [
                'management' => 'Verwaltung des physischen Codetyps',
            ],

            'information_type' => [
                'management' => 'Verwaltung von Informationstypen',
            ],

            'services' => [
                'management' => 'Service management',
            ],

            'actors' => [
                'management' => 'Schauspieler Management',
            ],

            'services_exclusive' => [
                'management' => 'Services Exclusive Management',
            ],

            'providers' => [
                'management' => 'Anbietermanagement',
            ],

            'provider_services' => [
                'management' => 'Provider Services Management',
            ],


            'profiles' => [
                'active' => 'Kundenprofile',
                'create' => 'Erstellen Sie ein Kundenprofil',
                'deactivated' => 'Deaktivierte Profile',
                'deleted' => 'Gelöschte Profile',
                'edit' => 'Profil bearbeiten',
                'management' => 'Client-Profilverwaltung',
                'no_permissions' => 'Keine Berechtigungen',
                'no_roles' => 'Keine zu setzenden Rollen.',
                'permissions' => 'Berechtigungen',
                'contact_type_actions' => 'Client-Profilaktionen',
                'view' => 'Client-Profil anzeigen',
                'client_profile' => 'Kundenprofil',
                'table' => [
                    'confirmed' => 'Bestätigt',
                    'created' => 'Erstellt',
                    'id' => 'ID',
                    'last_updated' => 'Letzte Aktualisierung',
                    'full_name' => 'Vollständiger Name',
                    'nickname' => 'Spitzname',
                    'for_what_brand' => 'Für welche Marke?',
                    'primary_email' => 'Erste Email',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Überblick',
                        'history' => 'Geschichte',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar' => 'Benutzerbild',
                            'confirmed' => 'Bestätigt',
                            'created_at' => 'Hergestellt in',
                            'deleted_at' => 'Gelöscht bei',
                            'email' => 'Email',
                            'last_login_at' => 'Letzter Login um',
                            'last_login_ip' => 'Letzte Login IP',
                            'last_updated' => 'Letzte Aktualisierung',
                            'name' => 'Name',
                            'first_name' => 'Vorname',
                            'last_name' => 'Nachname',
                            'status' => 'Status',
                            'timezone' => 'Zeitzone',
                        ],
                    ],
                ],

                'forms' => [
                    'title' => 'Kundenprofilformular',
                    'surname' => 'Nachname',
                    'firstname' => 'Vorname',
                    'middlename' => 'Zweiter Vorname',
                    'nickname' => 'Spitzname',
                    'primary_email' => 'Erste Email',
                    'add_notes_now' => 'Jetzt Notizen hinzufügen',
                    'date_requested' => 'Interaktionsdatum',
                    'notes_added_by' => 'Notizen hinzugefügt von',
                    'notes' => 'Anmerkungen',
                    'contacts' => 'Kontakte',
                    'jobs' => 'Arbeitsplätze',
                    'location' => 'Standort',
                    'country' => 'Land',
                    'province' => 'Provinz/Abteilung',
                    'city' => 'Stadt',
                    'add_notes_to' => 'Notizen hinzufügen zu',
                    'add_contacts_to' => 'Kontakte hinzufügen zu',
                    'note_form' => 'Notizen Formular',
                    'add_notes_now' => 'Jetzt Notizen hinzufügen',
                    'add_contacts_now' => 'Jetzt Kontakte hinzufügen',
                    'add_jobs_now' => 'Jetzt Jobs hinzufügen',
                    'add_client_engagements_now' => 'Fügen Sie jetzt Client-Engagements hinzu',
                    'contact_type' => "Kontaktart",
                    'contact_information' => "Kontaktdetails",
                    'select_contact_type' => "Wählen Sie Kontakttyp",
                    'create_new_contact_type' => "Neuen Kontakttyp hinzufügen",
                    'missing_surname' => "Nachname erforderlich",
                    'missing_firstname' => "Vorname (erforderlich",
                    'existing_notes' => 'Bestehende Notizen',
                    'new_notes' => 'Neue Notizen',
                    'brands' => 'Marken',
                    'select_brand' => 'Marke (n) auswählen',
                    'added_note' => 'Neue Notiz wurde hinzugefügt ',
                    'added_contact' => 'Neuer Kontakt wurde hinzugefügt ',
                    'add_new_province' => 'Neue Provinz hinzufügen',
                    'add_new_city' => 'Neue Stadt hinzufügen',
                    'add_job' => 'Job hinzufügen',
                    'add_contact' => 'Kontakt hinzufügen',
                    'add_note' => 'Notiz hinzufügen',

                    'see_existing_notes' =>  'Siehe vorhandene Hinweise',
                    'see_existing_contacts' =>  'Bestehende Kontakte anzeigen',

                    'New' =>  'Neu',
                    
                    'checking_database_for_matches' =>  'Datenbank auf Übereinstimmungen prüfen',
                    'warning_profile_with_same_name' =>  'WARNUNG: Gefundenes Profil mit demselben Namen. Die primäre E-Mail-Adresse für dieses Profil lautet',
                    'no_profile_with_same_name' =>  'Kein Profil mit demselben Namen gefunden',

                    'warning_profile_with_same_primary_email' =>  'FEHLER: Profil mit derselben primären E-Mail gefunden, der vollständige Name des Kunden lautet',
                    'no_profile_with_same_primary_email' =>  'Kein Profil mit der primären E-Mail gefunden',
                    
                    'job_category' =>  'Stellenkategorie',
                    'new_category' =>  'Neue Kategorie',
                    'profession' =>  'Beruf',
                    'new_profession' =>  'Neuer Beruf',
                    'description' =>  'Beschreibung',
                    'new_description' =>  'Neue Beschreibung',
                ],

                'view_profile' => [
                    'full_name' => 'Vollständiger Name',
                    'nickname' => 'Spitzname',
                    'primary_email' => 'Erste Email',
                    'location' => 'Standort',
                    'profession' => 'Beruf',
                    'edit_profile' => 'Profil bearbeiten',
                    'contacts' => 'Kontakte',
                    'contact_type' => 'kontaktart',
                    'contact_information' => 'Kontaktdetails',
                    'no_contacts' => 'Keine Kontakte gefunden',
                    'date_requested' => 'Interaktionsdatum',
                    'notes' => 'Anmerkungen',
                    'note_form' => 'Hinweisformular',
                    'add_note' => 'Notizen hinzufügen',
                    'edit_note' => 'Notiz bearbeiten',
                    'no_notes' => 'Keine Notizen gefunden',
                    'notes_added_by' => 'Notizen hinzugefügt von',
                    'see_clients_list' => 'Siehe Kundenliste',
                    'contact_form' => 'Kontakt Formular',
                    'add_contact' => 'Kontakte hinzufügen',
                    'edit_contact' => 'Kontakt bearbeiten',
                    'no_contacts' => 'Keine Kontakte gefunden',
                    'contacts_added_by' => 'Kontakte hinzugefügt von',
                    'client_engagements' => 'Kundenbindungen',
                    'add_client_engagements' => 'Client-Engagements hinzufügen',
                    'no_client_engagements' => 'Keine Kundenengagements gefunden',
                    'specialization' =>  'Spezialisierung',
                    'jobs' =>  'Arbeitsplätze',
                ],
                
            ],
        ],
    ],

    'frontend' => [
        'auth' => [
            'login_box_title' => 'Anmeldung',
            'login_button' => 'Anmelden',
            'login_with' => 'Anmelden mit :social_media',
            'register_box_title' => 'Registrieren',
            'register_button' => 'Registrieren',
            'remember_me' => 'An mich errinnern',
        ],

        'contact' => [
            'box_title' => 'Contact Us',
            'button' => 'Send Information',
        ],

        'passwords' => [
            'forgot_password' => 'Kennwort vergessen?',
            'reset_password_box_title' => 'Kennwort zurücksetzen',
            'reset_password_button' => 'Kennwort zurücksetzen',
            'update_password_button' => 'Kennwort aktualisieren',
            'send_password_reset_link_button' => 'Link zum zurücksetzen des Kennworts senden',
        ],

        'user' => [
            'passwords' => [
                'change' => 'Kennwort ändern',
            ],

            'profile' => [
                'avatar' => 'Avatar',
                'created_at' => 'Erstellt am',
                'edit_information' => 'Informationen bearbeiten',
                'email' => 'E-Mail',
                'last_updated' => 'Letzte Aktualisierung',
                'name' => 'Name',
                'first_name' => 'Vorname',
                'last_name' => 'Nachname',
                'update_information' => 'Informationen aktualisieren',
            ],
        ],
    ],
];
