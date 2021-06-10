<?php

use App\Models\Mails\Campaign;

Breadcrumbs::for('admin.dashboard', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.dashboard'));
});

Breadcrumbs::for('admin.contact_types.index', function ($trail) {
    $trail->push(__('strings.backend.contact_types.title'), route('admin.contact_types.index'));
});

Breadcrumbs::for('admin.profiles.index', function ($trail) {
    $trail->push(__('strings.backend.profiles.title'), route('admin.profiles.index'));
});

Breadcrumbs::for('admin.locations.index', function ($trail) {
    $trail->push(__('strings.backend.locations.title'), route('admin.locations.index'));
});

Breadcrumbs::for('admin.profiles.show', function ($trail) {
    $trail->push(__('strings.backend.profiles.show'), route('admin.profiles.show', ["id"]));
});

Breadcrumbs::for('admin.brands.index', function ($trail) {
    $trail->push(__('strings.backend.brands.title'), route('admin.brands.index'));
});

Breadcrumbs::for('admin.workspaces.index', function ($trail) {
    $trail->push(__('strings.backend.workspaces.title'), route('admin.workspaces.index'));
});

Breadcrumbs::for('admin.organization-categories.index', function ($trail) {
    $trail->push(__('strings.backend.organization_categories.title'), route('admin.organization-categories.index'));
});

Breadcrumbs::for('admin.attachments', function ($trail) {
    $trail->push(__('strings.backend.attachments.main'), route('admin.attachments.category'));
});

Breadcrumbs::for('admin.attachments.category', function ($trail) {
    $trail->parent('admin.attachments');
    $trail->push(__('strings.backend.attachments.category'), route('admin.attachments.category'));
});

Breadcrumbs::for('admin.attachments.type', function ($trail) {
    $trail->parent('admin.attachments');
    $trail->push(__('strings.backend.attachments.type'), route('admin.attachments.type'));
});

Breadcrumbs::for('admin.service-type.index', function ($trail) {
    $trail->push(__('strings.backend.service_type.title'), route('admin.service-type.index'));
});

Breadcrumbs::for('admin.provider-type.index', function ($trail) {
    $trail->push(__('strings.backend.provider_type.title'), route('admin.provider-type.index'));
});

Breadcrumbs::for('admin.article-content-maker.services.index', function ($trail) {
    $trail->push(__('strings.backend.services.title'), route('admin.article-content-maker.services.index'));
});

Breadcrumbs::for('admin.article-content-maker.services-exclusive.index', function ($trail) {
    $trail->push(__('strings.backend.services_exclusive.title'), route('admin.article-content-maker.services-exclusive.index'));
});

Breadcrumbs::for('admin.article-content-maker.providers.index', function ($trail) {
    $trail->push(__('strings.backend.providers.title'), route('admin.article-content-maker.providers.index'));
});

Breadcrumbs::for('admin.article-content-maker.provider-services.index', function ($trail) {
    $trail->push(__('strings.backend.provider_services.title'), route('admin.article-content-maker.provider-services.index'));
});

Breadcrumbs::for('admin.article-content-maker.provider-groups.index', function ($trail) {
    $trail->push(__('strings.backend.provider_groups.title'), route('admin.article-content-maker.provider-groups.index'));
});

Breadcrumbs::for('admin.article-content-maker.parameters.index', function ($trail) {
    $trail->push(__('strings.backend.parameters.title'), route('admin.article-content-maker.parameters.index'));
});

Breadcrumbs::for('admin.reports.statistics.index', function ($trail) {
    $trail->push(__('strings.backend.statistics.title'), route('admin.reports.statistics.index'));
});

// for jobs breadcrumbs
Breadcrumbs::for('admin.jobs', function ($trail) {
    $trail->push(__('strings.backend.jobs.title'), route('admin.jobs.category'));
});

Breadcrumbs::for('admin.jobs.category', function ($trail) {
    $trail->parent('admin.jobs');
    $trail->push(__('strings.backend.category.title'), route('admin.jobs.category'));
});

Breadcrumbs::for('admin.jobs.profession', function ($trail) {
    $trail->parent('admin.jobs');
    $trail->push(__('strings.backend.profession.title'), route('admin.jobs.profession'));
});

Breadcrumbs::for('admin.jobs.description', function ($trail) {
    $trail->parent('admin.jobs');
    $trail->push(__('strings.backend.description.title'), route('admin.jobs.description'));
});


// BREADCRUM FOR MEDICAL STUFF ITEM
Breadcrumbs::for('admin.medical-stuff.body-parts', function ($trail) {
    $trail->push(__('menus.backend.sidebar.body_parts'), route('admin.medical-stuff.body-parts'));
});
Breadcrumbs::for('admin.medical-stuff.structure-type', function ($trail) {
    $trail->push(__('menus.backend.sidebar.structure_types'), route('admin.medical-stuff.structure-type'));
});
Breadcrumbs::for('admin.medical-stuff.term-type', function ($trail) {
    $trail->push(__('menus.backend.sidebar.term_types'), route('admin.medical-stuff.term-type'));
});
Breadcrumbs::for('admin.medical-stuff.specializations', function ($trail) {
    $trail->push(__('menus.backend.sidebar.specializations'), route('admin.medical-stuff.specializations'));
});
Breadcrumbs::for('admin.medical-stuff.medical-term', function ($trail) {
    $trail->push(__('menus.backend.sidebar.medical_term'), route('admin.medical-stuff.medical-term'));
});
Breadcrumbs::for('admin.medical-stuff.medical-articles', function ($trail) {
    $trail->push(__('menus.backend.sidebar.medical_articles'), route('admin.medical-stuff.medical-articles'));
});


Breadcrumbs::for('admin.actions', function ($trail) {
    $trail->push(__('strings.backend.actions.title'), route('admin.actions.activity'));
});

Breadcrumbs::for('admin.actions.activity', function ($trail) {
    $trail->parent('admin.actions');
    $trail->push(__('strings.backend.actions.activity.title'), route('admin.actions.activity'));
});

//actions Breadcrumbs
Breadcrumbs::for('admin.actions.engagements', function ($trail) {
    $trail->parent('admin.actions');
    $trail->push(__('strings.backend.engagements.title'), route('admin.actions.engagements'));
});
Breadcrumbs::for('admin.actions.administrative', function ($trail) {
    $trail->parent('admin.actions');
    $trail->push(__('strings.backend.administrative.title'), route('admin.actions.administrative'));
});


// BREADCRUM FOR PLATFORM TYPE
Breadcrumbs::for('admin.platform-type', function ($trail) {
    $trail->push(__('strings.backend.platform.type'), route('admin.platform-type'));
});

// BREADCRUM FOR PLATFORM ITEM
Breadcrumbs::for('admin.platform-item', function ($trail) {
    $trail->push(__('strings.backend.platform.main'), route('admin.platform-item'));
});

// BREADCRUM FOR TAGS
Breadcrumbs::for('admin.publishing-tools.tags', function ($trail) {
    $trail->push(__('menus.backend.tags.main'), route('admin.publishing-tools.tags'));
});

// BREADCRUM FOR DATES
Breadcrumbs::for('admin.publishing-tools.dates', function ($trail) {
    $trail->push(__('menus.backend.dates.main'), route('admin.publishing-tools.dates'));
});


Breadcrumbs::for('admin.publishing-tools.content', function ($trail) {
    $trail->push(__('menus.backend.content.main'), route('admin.publishing-tools.content'));
});

Breadcrumbs::for('admin.publishing-tools.publishing', function ($trail) {
    $trail->push(__('menus.backend.publishing.main'), route('admin.publishing-tools.publishing'));
});

Breadcrumbs::for('admin.publishing-tools.items', function ($trail) {
    $trail->push(__('menus.backend.items.main'), route('admin.publishing-tools.items'));
});

Breadcrumbs::for('admin.publishing-tools.content.history', function ($trail) {
    $trail->push(__('menus.backend.content.main'), route('admin.publishing-tools.content.history'));
});

Breadcrumbs::for('admin.publishing-tools.items.history', function ($trail) {
    $trail->push(__('menus.backend.content.main'), route('admin.publishing-tools.items.history'));
});

Breadcrumbs::for('admin.publishing-tools.publishing.history', function ($trail) {
    $trail->push(__('menus.backend.publishing.main'), route('admin.publishing-tools.publishing.history'));
});

Breadcrumbs::for('admin.publishing-tools.categories', function ($trail) {
    $trail->push(__('menus.backend.publishing.main'), route('admin.publishing-tools.categories'));
});


// BREADCRUM FOR PUBLISHING TYPE
Breadcrumbs::for('admin.item-type', function ($trail) {
    $trail->push(__('menus.backend.publishing.type'), route('admin.item-type'));
});

// BREADCRUM FOR PUBLISHING ITEM
Breadcrumbs::for('admin.publishing-item', function ($trail) {
    $trail->push(__('menus.backend.publishing.item'), route('admin.publishing-item'));
});

// for Brands2
Breadcrumbs::for('admin.brands2', function ($trail) {
    $trail->push(__('menus.backend.brands.main'), route('admin.brands2'));
});

// Campaign Breadcrumbs
Breadcrumbs::for('admin.campaign.index', function ($trail) {
    $trail->push(__('menus.backend.campaigns.main'), route('admin.campaign.index'));
});

// Templates Breadcrumbs
Breadcrumbs::for('admin.templates.index', function ($trail) {
    $trail->push(__('menus.backend.campaigns.templates'), route('admin.templates.index'));
});

Breadcrumbs::for('admin.workforce-management.index', function ($trail) {
    $trail->push(__('menus.backend.workforce_management.main'), route('admin.workforce-management.index'));
});

Breadcrumbs::for('admin.workforce-management.request-type.index', function ($trail) {
    $trail->parent('admin.workforce-management.index');
    $trail->push(__('menus.backend.workforce_management.request_type'), route('admin.workforce-management.request-type.index'));
});
Breadcrumbs::for('admin.workforce-management.reasons.index', function ($trail) {
    $trail->parent('admin.workforce-management.index');
    $trail->push(__('menus.backend.workforce_management.reasons'), route('admin.workforce-management.reasons.index'));
});
Breadcrumbs::for('admin.workforce-management.color-coding.index', function ($trail) {
    $trail->parent('admin.workforce-management.index');
    $trail->push(__('menus.backend.workforce_management.color_coding'), route('admin.workforce-management.color-coding.index'));
});
Breadcrumbs::for('admin.workforce-management.approval-settings.index', function ($trail) {
    $trail->parent('admin.workforce-management.index');
    $trail->push(__('menus.backend.workforce_management.approval_settings'), route('admin.workforce-management.approval-settings.index'));
});
Breadcrumbs::for('admin.workforce-management.default-groups.index', function ($trail) {
    $trail->parent('admin.workforce-management.index');
    $trail->push(__('menus.backend.workforce_management.default_groups'), route('admin.workforce-management.default-groups.index'));
});
Breadcrumbs::for('admin.workforce-management.calendar-notes.index', function ($trail) {
    $trail->parent('admin.workforce-management.index');
    $trail->push(__('menus.backend.workforce_management.calendar_notes'), route('admin.workforce-management.calendar-notes.index'));
});
Breadcrumbs::for('admin.workforce-management.calendar-notes-type.index', function ($trail) {
    $trail->parent('admin.workforce-management.index');
    $trail->push(__('menus.backend.workforce_management.calendar_notes_type'), route('admin.workforce-management.calendar-notes-type.index'));
});
Breadcrumbs::for('admin.campaign.show', function ($trail, $id) {
    $trail->parent('admin.campaign.index');
    $campaign  = Campaign::findOrFail($id);
    $trail->push($campaign->campaign_name, route('admin.campaign.show', $id));
});





require __DIR__ . '/auth.php';
require __DIR__ . '/log-viewer.php';
