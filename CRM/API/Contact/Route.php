<?php

use CRM\API\Contact\ContactController;

    Route::get('/contact_matches', [ContactController::class, 'find_match']);