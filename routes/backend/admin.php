<?php

use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\ContactTypeController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\ContactController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\PublishingController;
use App\Http\Controllers\Backend\ContentController;
use App\Http\Controllers\Backend\ContentItemController;
use App\Http\Controllers\Backend\ItemController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\TagsController;
use App\Http\Controllers\Backend\DateStatusController;
use App\Http\Controllers\Backend\GlobalViewController;
use App\Http\Controllers\Backend\CourseController;

// All route names are prefixed with 'admin.'.
Route::redirect('/', '/admin/dashboard', 301);
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');


// All route names are prefixed with 'admin.products.'.


Route::group([
    'prefix' => 'contact_types',
    'as' => 'contact_types.',    //config('access.users.default_role')
], function () {
    Route::get('/all', [ContactTypeController::class, 'show_all'])->name('all');
    //For vue version Brands
    Route::get('/', [GlobalViewController::class, 'contact_types'])->name('index');
    Route::post('/', [ContactTypeController::class, 'store'])->name('store');
    //Route::patch('/', [ContactTypeController::class, 'update'])->name('update');

    // Specific Item
    Route::group(['prefix' => '/{id}'], function () {
        Route::get('/', [ContactTypeController::class, 'show'])->name('show');
        Route::get('/view', [ContactTypeController::class, 'view'])->name('view');
        Route::put('/', [ContactTypeController::class, 'update'])->name('update');
        Route::delete('/', [ContactTypeController::class, 'destroy'])->name('destroy');
    });
});

/*
// All route names are prefixed with 'admin.products.'.
Route::group([
    'prefix' => 'profiles',
    'as' => 'profiles.',    //config('access.users.default_role')
], function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::post('/', [ProfileController::class, 'store'])->name('store');
    Route::patch('/', [ProfileController::class, 'update'])->name('update');

    // Specific Item
    Route::group(['prefix' => '/{id}'], function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/view', [ProfileController::class, 'view'])->name('view');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});
*/

Route::group(['prefix' => 'statistics'], function () {
    Route::group(['prefix' => 'graph'], function () {
        Route::group(['prefix' => 'location'], function () {
            Route::get('country', [ProfileController::class, 'country_location_statistics'])->name('country_location_statistics');
            Route::get('province', [ProfileController::class, 'province_location_statistics'])->name('province_location_statistics');
            Route::get('city', [ProfileController::class, 'city_location_statistics'])->name('city_location_statistics');
        });

        Route::group(['prefix' => 'job'], function () {
            Route::get('category', [ProfileController::class, 'category_job_statistics'])->name('category_job_statistics');
            Route::get('profession', [ProfileController::class, 'profession_job_statistics'])->name('profession_job_statistics');
            Route::get('specialization', [ProfileController::class, 'specialization_job_statistics'])->name('specialization_job_statistics');
        });
    });

    Route::group(['prefix' => 'tabular'], function () {
        Route::get('location', [ProfileController::class, 'tabular_location_statistics'])->name('tabular_location_statistics');
        Route::get('job', [ProfileController::class, 'tabular_job_statistics'])->name('tabular_job_statistics');

        Route::group(['prefix' => 'detail'], function () {
            Route::get('job', [ProfileController::class, 'detailed_job_stats'])->name('detailed_job_stats');
            Route::get('location', [ProfileController::class, 'detailed_location_stats'])->name('detailed_location_stats');
        });

        Route::group(['prefix' => 'export'], function () {
            Route::get('job', [ProfileController::class, 'export_job_stats'])->name('export_job_stats');
            Route::get('location', [ProfileController::class, 'export_location_stats'])->name('export_location_stats');
        });
    });
});

Route::get('profiles/', [GlobalViewController::class, 'profiles'])->name('profiles.index');
Route::get('view_profile/{id}', [GlobalViewController::class, 'show_profile'])->name('profiles.show');

Route::get('locations/', [GlobalViewController::class, 'locations'])->name('locations.index');

// Jobs Routes/
Route::group(['prefix' => 'jobs'], function () {
    Route::get('/', [GlobalViewController::class, 'job_category'])->name('jobs');
    Route::get('category', [GlobalViewController::class, 'job_category'])->name('jobs.category');
    Route::get('profession', [GlobalViewController::class, 'job_profession'])->name('jobs.profession');
    Route::get('description', [GlobalViewController::class, 'job_description'])->name('jobs.description');
});

Route::group(['prefix' => 'publishing-tools', 'as' => 'publishing-tools.',], function () {

    Route::group(['prefix' => 'publishing'], function () {
        Route::get('/', [PublishingController::class, 'index'])->name('publishing');
        Route::get('/getList', [PublishingController::class, 'getList'])->name('publishing.getList');
        Route::get('/item', [PublishingController::class, 'show'])->name('publishing.show');
        Route::get('/history', [PublishingController::class, 'showHistory'])->name('publishing.history');
        Route::get('/getHistory', [PublishingController::class, 'getHistory'])->name('publishing.getHistory');
        Route::get('/getUsers', [PublishingController::class, 'getUsers'])->name('publishing.getUsers');
        Route::get('/getTags', [PublishingController::class, 'getTags'])->name('publishing.getTags');
        Route::get('/getUsersAndItems', [PublishingController::class, 'getUsersAndItems'])->name('publishing.getUsersAndItems');
        Route::get('/getBrandsAndPlatformTypes', [PublishingController::class, 'getBrandsAndPlatformTypes'])->name('publishing.getBrandsAndPlatformTypes');
        Route::get('/getOtherTags', [PublishingController::class, 'getOtherTags'])->name('publishing.getOtherTags');
        Route::get('/getPublishingRelation/{publishing}', [PublishingController::class, 'getPublishingRelation'])->name('publishing.getPublishingRelation');
        Route::get('/getNextStatus/{id}', [PublishingController::class, 'getNextStatus'])->name('publishing.getNextStatus');
        Route::post('/postPublishing', [PublishingController::class, 'postPublishing'])->name('publishing.postPublishing');
        Route::post('/attachTags', [PublishingController::class, 'attachTags'])->name('publishing.attachTags');
        Route::post('/removeTags', [PublishingController::class, 'removeTags'])->name('publishing.removeTags');
        Route::post('/createLink', [PublishingController::class, 'createLink'])->name('publishing.createLink');
        Route::post('/updatePublishing', [PublishingController::class, 'updatePublishing'])->name('publishing.updatePublishing');
        Route::post('/updateStatus', [PublishingController::class, 'updateStatus'])->name('publishing.updateStatus');
        Route::get('/deletePublishing/{id}', [PublishingController::class, 'deletePublishing'])->name('publishing.deletePublishing');
        Route::get('/getPublishingName/{id}/{lang}', [PublishingController::class, 'getPublishingName'])->name('publishing.getPublishingName');
    });

    Route::group(['prefix' => 'date-status'], function () {
        Route::get('/', [DateStatusController::class, 'index'])->name('dates');
        Route::get('/getList', [DateStatusController::class, 'getList'])->name('dates.getList');
        Route::get('/getDateStatus', [DateStatusController::class, 'getDateStatus'])->name('dates.getDateStatus');
        Route::post('/', [DateStatusController::class, 'store'])->name('dates.store');
        Route::post('/update', [DateStatusController::class, 'update'])->name('dates.update');
        Route::post('/changeOrder', [DateStatusController::class, 'changeOrder'])->name('dates.changeOrder');
        Route::get('/deleteDate/{id}', [DateStatusController::class, 'destroy'])->name('dates.delete');
        Route::get('/getDateStatusName/{id}/{lang}', [DateStatusController::class, 'getDateStatusName'])->name('dates.getDateStatusName');
    });

    Route::group(['prefix' => 'content'], function () {
        Route::get('/', [ContentController::class, 'index'])->name('content');
        Route::get('/getList', [ContentController::class, 'getList'])->name('content.getList');
        Route::get('/item', [ContentController::class, 'show'])->name('content.show');
        Route::get('/history', [ContentController::class, 'showHistory'])->name('content.history');
        Route::get('/users', [ContentController::class, 'fetchUsers'])->name('content.users');
        Route::get('/getHistory', [ContentController::class, 'getHistory'])->name('content.getHistory');
        Route::get('/getLastStatus', [ContentController::class, 'getLastStatus'])->name('content.getLastStatus');
        Route::get('/getItemStatus', [ContentController::class, 'getItemStatus'])->name('content.getItemStatus');
        Route::get('/getNextStatus/{id}', [ContentController::class, 'getNextStatus'])->name('content.getNextStatus');
        Route::post('/postContent', [ContentController::class, 'postContent'])->name('content.postContent');
        Route::post('/updateContent', [ContentController::class, 'updateContent'])->name('content.updateContent');
        Route::post('/updateStatus', [ContentController::class, 'updateStatus'])->name('content.updateStatus');
        Route::get('/deleteContent/{id}', [ContentController::class, 'deleteContent'])->name('content.deleteContent');
        Route::get('/getContentName/{id}/{lang}', [ContentController::class, 'getContentName'])->name('content.getContentName');

        Route::group(['prefix' => 'items'], function () {
            Route::get('/', [ContentItemController::class, 'index'])->name('items');
            Route::get('/history', [ContentItemController::class, 'showHistory'])->name('items.history');
            Route::get('/getHistory', [ContentItemController::class, 'getHistory'])->name('items.getHistory');
            Route::get('/getList', [ContentItemController::class, 'getList'])->name('items.getList');
            Route::get('/getNextStatus/{id}', [ContentItemController::class, 'getNextStatus'])->name('items.getNextStatus');
            Route::get('/getLastStatus', [ContentItemController::class, 'getLastStatus'])->name('items.getLastStatus');
            Route::get('/getBrandsAndPlatformTypes', [ContentItemController::class, 'getBrandsAndPlatformTypes'])->name('items.getBrandsAndPlatformTypes');
            Route::get('/getUsersAndItemTypes', [ContentItemController::class, 'getUsersAndItemTypes'])->name('items.getUsersAndItemTypes');
            Route::post('/', [ContentItemController::class, 'store'])->name('items.store');
            Route::post('/update', [ContentItemController::class, 'update'])->name('items.update');
            Route::post('/updateStatus', [ContentItemController::class, 'updateStatus'])->name('items.updateStatus');
            Route::post('/createPublishing', [ContentItemController::class, 'createPublishing'])->name('items.createPublishing');
            Route::get('/deleteItem/{id}', [ContentItemController::class, 'destroy'])->name('items.delete');
            Route::get('/getTranslatedItemName/{id}/{lang}', [ContentItemController::class, 'getTranslatedItemName'])->name('items.getTranslatedItemName');
        });
    });

    Route::group(['prefix' => 'items'], function () {
        Route::get('/', [ItemController::class, 'index'])->name('items');
        Route::get('/getList', [ItemController::class, 'getList'])->name('items.getList');
        Route::get('/getUsersAndItemTypes', [ItemController::class, 'getUsersAndItemTypes'])->name('items.getUsersAndItemTypes');
        Route::get('/getContentUsersAndItemType', [ItemController::class, 'getContentUsersAndItemType'])->name('items.getContentUsersAndItemType');
        Route::post('/', [ItemController::class, 'store'])->name('items.store');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', [CategoryController::class, 'index'])->name('categories');
        Route::get('/getList', [CategoryController::class, 'getList'])->name('categories.getList');
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.delete');
        Route::get('/getCategoryName/{id}/{lang}', [CategoryController::class, 'getCategoryName'])->name('categories.getCategoryName');
    });

    Route::group(['prefix' => 'tags'], function () {
        Route::get('/', [TagsController::class, 'index'])->name('tags');
        Route::get('/getList', [TagsController::class, 'getList'])->name('tags.getList');
        Route::post('/postTags', [TagsController::class, 'postTags'])->name('tags.postTags');
        Route::post('/updateTags', [TagsController::class, 'updateTags'])->name('tags.updateTags');
        Route::get('/deleteTags/{id}', [TagsController::class, 'deleteTags'])->name('tags.deleteTags');
        Route::get('/getTagsName/{id}/{lang}', [TagsController::class, 'getTagsName'])->name('tags.getTagsName');
    });
});

Route::group(['prefix' => 'article-content-maker', 'as' => 'article-content-maker.',], function () {

    Route::group([
        'prefix' => 'services',
        'as' => 'services.',
    ], function () {

        Route::get('/', [GlobalViewController::class, 'services'])->name('index');
    });

    Route::group([
        'prefix' => 'geolocalizations',
        'as' => 'geolocalizations.',
    ], function () {

        Route::get('/', [GlobalViewController::class, 'geolocalizations'])->name('index');
        Route::get('/{id}', [GlobalViewController::class, 'geolocalizations_show'])->name('show');
    });

    Route::group([
        'prefix' => 'actors',
        'as' => 'actors.',
    ], function () {

        Route::get('/', [GlobalViewController::class, 'actors'])->name('index');
    });

    Route::group([
        'prefix' => 'services-exclusive',
        'as' => 'services-exclusive.',
    ], function () {

        Route::get('/', [GlobalViewController::class, 'services_exclusive'])->name('index');
    });

    Route::group([
        'prefix' => 'parameters',
        'as' => 'parameters.',
    ], function () {

        Route::get('/', [GlobalViewController::class, 'parameters'])->name('index');
    });

    Route::group([
        'prefix' => 'providers',
        'as' => 'providers.',
    ], function () {

        Route::get('/', [GlobalViewController::class, 'providers'])->name('index');
    });

    Route::group([
        'prefix' => 'provider-services',
        'as' => 'provider-services.',
    ], function () {

        Route::get('/', [GlobalViewController::class, 'provider_services'])->name('index');
    });

    Route::group([
        'prefix' => 'provider-groups-and-providers',
        'as' => 'provider-groups-and-providers.',
    ], function () {

        Route::get('/', [GlobalViewController::class, 'provider_groups_and_providers'])->name('index');
    });

    Route::group([
        'prefix' => 'provider-groups',
        'as' => 'provider-groups.',
    ], function () {

        Route::get('/', [GlobalViewController::class, 'provider_groups'])->name('index');
    });

    Route::group([
        'prefix' => 'algolia-permissions',
        'as' => 'algolia-permissions.',
    ], function () {

        Route::get('/', [GlobalViewController::class, 'algolia_permissions'])->name('index');
    });

    Route::group([
        'prefix' => 'algolia-classes',
        'as' => 'algolia-classes.',
    ], function () {

        Route::get('/', [GlobalViewController::class, 'algolia_classes'])->name('index');
    });

    // Route::group([
    //     'prefix' => 'statistics',
    //     'as' => 'statistics.',
    // ], function () {

    //     Route::get('/', [GlobalViewController::class, 'statistics'])->name('index');
    // });
});

// Route::group(['prefix' => 'reports', 'as' => 'reports.',], function () {

//     Route::group([
//         'prefix' => 'statistics',
//         'as' => 'statistics.',
//     ], function () {

//         Route::get('/', [GlobalViewController::class, 'statistics'])->name('index');
//     });
// });

// Action Routes/
Route::group(['prefix' => 'actions'], function () {
    Route::get('activity', [GlobalViewController::class, 'activity'])->name('actions.activity');
    Route::get('engagements', [GlobalViewController::class, 'engagement'])->name('actions.engagements');
    Route::get('administrative', [GlobalViewController::class, 'administrative'])->name('actions.administrative');
});

Route::post('/link_to_brand/{id}', [ProfileController::class, 'link_to_brand'])->name('profile.link_to_brand');

Route::get('/get_notes/{id}', [ProfileController::class, 'get_notes'])->name('profile.get_notes');
Route::post('/add_notes/{id}', [ProfileController::class, 'add_notes'])->name('profile.add_notes');
Route::post('/update_note/{id}', [ProfileController::class, 'update_note'])->name('profile.update_note');

Route::get('/get_contacts/{id}', [ProfileController::class, 'get_contacts'])->name('profile.get_contacts');
Route::post('/add_contacts/{id}', [ProfileController::class, 'add_contacts'])->name('profile.add_contacts');
Route::post('/update_contact/{id}', [ProfileController::class, 'update_contact'])->name('profile.update_contact');

//Route::get('/view_profile/{id}', [ProfileController::class, 'view'])->name('profile.view');
Route::post('/profile_matches', [ProfileController::class, 'find_match'])->name('profiles.find_match');


// All route names are prefixed with 'admin.products.'.
Route::group([
    'prefix' => 'contacts',
    'as' => 'contacts.',    //config('access.users.default_role')
], function () {
    Route::get('/', [ContactController::class, 'index'])->name('index');
    Route::post('/', [ContactController::class, 'store'])->name('store');
    Route::patch('/', [ContactController::class, 'update'])->name('update');

    // Specific Item
    Route::group(['prefix' => '/{id}'], function () {
        Route::get('/', [ContactController::class, 'show'])->name('show');
        Route::get('/view', [ContactController::class, 'view'])->name('view');
        Route::put('/', [ContactController::class, 'update'])->name('update');
        Route::delete('/', [ContactController::class, 'destroy'])->name('destroy');
    });
});

Route::post('/contact_matches', [ContactController::class, 'find_match'])->name('contacts.find_match');

// All route names are prefixed with 'admin.products.'.
Route::group([
    'prefix' => 'brands',
    'as' => 'brands.',    //config('access.users.default_role')
], function () {
    Route::get('/all', [BrandController::class, 'show_all'])->name('all');
    Route::get('/swap/{id}', [BrandController::class, 'swap'])->name('swap');
    //For vue version Brands
    Route::get('/', [GlobalViewController::class, 'brands'])->name('index');
    Route::post('/', [BrandController::class, 'store'])->name('store');
    //Route::patch('/', [BrandController::class, 'update'])->name('update');

    // Specific Item
    Route::group(['prefix' => '/{id}'], function () {
        Route::get('/', [BrandController::class, 'show'])->name('show');
        Route::get('/view', [BrandController::class, 'view'])->name('view');
        Route::put('/', [BrandController::class, 'update'])->name('update');
        Route::delete('/', [BrandController::class, 'destroy'])->name('destroy');
    });
});

Route::group([
    'prefix' => 'workspaces',
    'as' => 'workspaces.',    //config('access.users.default_role')
], function () {
    Route::get('/', [GlobalViewController::class, 'workspace'])->name('index');
});

Route::group([
    'prefix' => 'organization-categories',
    'as' => 'organization-categories.',    //config('access.users.default_role')
], function () {
    //For vue version Brands
    Route::get('/', [GlobalViewController::class, 'organization_categories'])->name('index');
    //Route::patch('/', [BrandController::class, 'update'])->name('update');
});


Route::group(['prefix' => 'attachments'], function () {
    Route::get('/', [GlobalViewController::class, 'attachments_category'])->name('attachments');
    Route::get('category', [GlobalViewController::class, 'attachments_category'])->name('attachments.category');
    Route::get('type', [GlobalViewController::class, 'attachments_type'])->name('attachments.type');
});

Route::group([
    'prefix' => 'service-type',
    'as' => 'service-type.',    //config('access.users.default_role')
], function () {
    //For vue version Brands
    Route::get('/', [GlobalViewController::class, 'service_type'])->name('index');
    //Route::patch('/', [BrandController::class, 'update'])->name('update');
});

Route::group([
    'prefix' => 'provider-type',
    'as' => 'provider-type.',    //config('access.users.default_role')
], function () {
    //For vue version Brands
    Route::get('/', [GlobalViewController::class, 'provider_type'])->name('index');
    //Route::patch('/', [BrandController::class, 'update'])->name('update');
});

Route::group([
    'prefix' => 'provider-group',
    'as' => 'provider-group.',    //config('access.users.default_role')
], function () {
    //For vue version Brands
    Route::get('/', [GlobalViewController::class, 'provider_type'])->name('index');
    //Route::patch('/', [BrandController::class, 'update'])->name('update');
});

Route::group([
    'prefix' => 'physical-code-type',
    'as' => 'physical-code-type.',    //config('access.users.default_role')
], function () {
    //For vue version Brands
    Route::get('/', [GlobalViewController::class, 'physical_code_type'])->name('index');
    //Route::patch('/', [BrandController::class, 'update'])->name('update');
});
Route::group([
    'prefix' => 'information-type',
    'as' => 'information-type.',    //config('access.users.default_role')
], function () {
    //For vue version Brands
    Route::get('/', [GlobalViewController::class, 'information_type'])->name('index');
    //Route::patch('/', [BrandController::class, 'update'])->name('update');
});


//For vue version Brands
Route::get('brands2', [GlobalViewController::class, 'brands'])->name('brands2');


// campaigns
Route::group(['prefix' => 'campaigns'], function () {
    Route::get('/', [GlobalViewController::class, 'campaign_index'])->name('campaign.index');
});

// campaigns
Route::group(['prefix' => 'templates'], function () {
    Route::get('/', [GlobalViewController::class, 'template_index'])->name('templates.index');
});

// campaigns
Route::group(['prefix' => 'workforce-management'], function () {
    Route::get('/', [GlobalViewController::class, 'workforce_management_index'])->name('workforce-management.index');
    Route::get('/request-type', [GlobalViewController::class, 'request_type_index'])->name('workforce-management.request-type.index');
    Route::get('/reasons', [GlobalViewController::class, 'reasons_index'])->name('workforce-management.reasons.index');
    Route::get('/color-coding', [GlobalViewController::class, 'color_coding'])->name('workforce-management.color-coding.index');
    Route::get('/approval-settings', [GlobalViewController::class, 'approval_settings'])->name('workforce-management.approval-settings.index');
    Route::get('/default-groups', [GlobalViewController::class, 'default_groups'])->name('workforce-management.default-groups.index');
    Route::get('/calendar-notes', [GlobalViewController::class, 'calendar_notes'])->name('workforce-management.calendar-notes.index');
    Route::get('/calendar-notes-type', [GlobalViewController::class, 'calendar_notes_type'])->name('workforce-management.calendar-notes-type.index');
});

Route::group(['prefix' => 'courses'], function () {

     Route::get('/type', [CourseController::class, 'course_type'])->name('courses.course_type');

});

// questions
Route::group(['prefix' => 'questions'], function () {
    Route::get('choices', [GlobalViewController::class, 'question_choices'])->name('questions.choices');
    Route::get('types', [GlobalViewController::class, 'question_types_index'])->name('questions.questions-types.index');
    Route::get('question-list', [GlobalViewController::class, 'question_list_index'])->name('questions.question-list.index');
});

Route::group(['prefix' => 'results'], function () {
    Route::get('/', [GlobalViewController::class, 'result_list_index'])->name('result-list-index');
});

// questions
Route::group(['prefix' => 'payment'], function () {
    Route::get('plans', [GlobalViewController::class, 'payment_plans'])->name('payment.plans');
});

Route::group(['prefix' => 'procedures'], function () {
    Route::get('/', [GlobalViewController::class, 'procedure_list_index'])->name('procedure-list-index');
});

// for email format test
// Route::get('email', [GlobalViewController::class, 'campaign_index'])->name('campaign.index');