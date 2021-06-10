import Vue from "vue";
import Vuex from "vuex";

import actor from "./modules/actor.module";
import brand from "./modules/brand.module";
import profile from "./modules/profile.module";
import platforms from "./modules/platforms.module";
import contact_types from "./modules/contact-type.module";
import jobs from "./modules/jobs.module";
import actions from "./modules/actions.module";
import campaigns from "./modules/campaign.module";
import location from "./modules/location.module";
import email_template from "./modules/email_template.module";
import organization_categories from "./modules/organization_categories.module";
import service_type from "./modules/service_type.module";
import services from "./modules/services.module";
import services_exclusive from "./modules/services_exclusive.module";
import provider_type from "./modules/provider_type.module";
import provider_group from "./modules/provider_group.module";
import providers from "./modules/providers.module";
import country from "./modules/country.module";
import division from "./modules/division.module";
import city from "./modules/city.module";
import provider_service from "./modules/provider_service.module";
import physical_code_type from "./modules/physical_code_type.module";
import information_type from "./modules/information_type.module";
import organization from "./modules/organization.module";
import attachments from "./modules/attachments.module";
import parameter from "./modules/parameter.module";
import workspace from "./modules/workspace.module";
import request_type from "./modules/request_type.module";
import reasons from "./modules/reasons.module";
import color_coding from "./modules/color_coding.module";
import approval_settings from "./modules/approval_settings.module";
import default_groups from "./modules/default_groups.module";
import calendar_notes from "./modules/calendar_notes.module";
import calendar_notes_type from "./modules/calendar_notes_type.module";
import categ_terms from "./modules/categ_terms.module";
import geolocalization from "./modules/geolocalization.module";
import geolocalization_template from "./modules/geolocalization_template.module";
import reports from "./modules/report.module";
import publish_content from "./modules/publish_content.module";
import prefilledsection from "./modules/prefilledsection.module";
import html_tags from "./modules/html_tags.module";
import question_choice from "./modules/question_choices.module";
import question_types from "./modules/question-types";
import questions from "./modules/questions";
import algolia from "./modules/algolia.module";
import algolia_permission from "./modules/algolia_permission.module";
import algolia_class from "./modules/algolia_class.module";
import payment_plan from './modules/payment_plan.module';
import course from './modules/courses.module';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    algolia,
    course,
    brand,
    actor,
    jobs,
    actions,
    campaigns,
    contact_types,
    profile,
    platforms,
    location,
    email_template,
    organization_categories,
    service_type,
    services,
    services_exclusive,
    provider_type,
    provider_group,
    providers,
    country,
    division,
    city,
    provider_service,
    physical_code_type,
    information_type,
    organization,
    attachments,
    parameter,
    workspace,
    request_type,
    reasons,
    color_coding,
    approval_settings,
    default_groups,
    calendar_notes,
    calendar_notes_type,
    categ_terms,
    geolocalization,
    geolocalization_template,
    reports,
    publish_content,
    prefilledsection,
    html_tags,
    question_choice,
    question_types,
    questions,
    algolia_permission,
    algolia_class,
    payment_plan
  },
});
