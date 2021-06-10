<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Mails\Campaign;
use App\Models\Profile;
use App\Models\MedicalStuff\MedicalArticle;

class GlobalViewController extends Controller
{
   // Profiles View Here
   public function profiles()
   {
      return view('backend.pages.profiles.index');
   }

   // Profiles View Here
   public function show_profile($id)
   {
      $profile_id = $id;
      return view('backend.pages.profiles.show', compact('profile_id'));
   }

   // Locations View Here
   public function locations()
   {
      return view('backend.pages.locations.index');
   }

   // Locations View Here
   public function statistics()
   {
      return view('backend.pages.statistics.index');
   }

   // Brands View Here
   public function brands()
   {
      return view('backend.pages.brands.index');
   }


   public function contact_types()
   {
      return view('backend.pages.contact_types.index');
   }

   // Jobs View Here
   public function job_category()
   {
      return view('backend.pages.jobs.category');
   }

   public function job_profession()
   {
      return view('backend.pages.jobs.profession');
   }

   public function job_description()
   {
      return view('backend.pages.jobs.description');
   }

   /**
    * activity
    *
    * @return void
    */
   public function algolia_permissions()
   {
      return view('backend.pages.article_content_maker.algolia_permission.index');
   }

   public function algolia_classes()
   {
      return view('backend.pages.article_content_maker.algolia_class.index');
   }
   
   public function activity()
   {
      return view('backend.pages.actions.activity');
   }

   // Engagement View Here
   public function engagement()
   {
      return view('backend.pages.actions.engagement');
   }

   public function administrative()
   {
      return view('backend.pages.actions.administrative');
   }

   // Campaign Mail
   public function campaign_index()
   {
      return view('backend.pages.campaign.index');

      // $profile = Profile::findOrFail(3);
      // $campaign = Campaign::findOrFail(3);
      // return view('backend.emails.campaign', compact('campaign', 'profile'));
   }

   // Campaign Mail
   public function template_index()
   {
      return view('backend.pages.template.index');

      // $profile = Profile::findOrFail(3);
      // $campaign = Campaign::findOrFail(3);
      // return view('backend.emails.campaign', compact('campaign', 'profile'));
   }

   // Campaign Workspace
   public function workforce_management_index() {
      return view('backend.pages.workforce-management.index');
   }

   public function request_type_index() {
      return view('backend.pages.workforce-management.request-type.index');
   }

   public function reasons_index() {
      return view('backend.pages.workforce-management.reasons.index');
   }

   public function color_coding() {
      return view('backend.pages.workforce-management.color-coding.index');
   }

   public function approval_settings() {
      return view('backend.pages.workforce-management.approval-settings.index');
   }

   public function calendar_notes() {
      return view('backend.pages.workforce-management.calendar-notes.index');
   }

   public function calendar_notes_type() {
      return view('backend.pages.workforce-management.calendar-notes-type.index');
   }

   public function default_groups() {
      return view('backend.pages.workforce-management.default-groups.index');
   }

   public function organization_categories()
   {
      return view('backend.pages.organization_category.index');
   }

   public function attachments_category()
   {
      return view('backend.pages.attachments.category');
   }

   public function attachments_type()
   {
      return view('backend.pages.attachments.type');
   }

   public function service_type()
   {
      return view('backend.pages.service_type.index');
   }

   public function provider_type()
   {
      return view('backend.pages.provider_type.index');
   }

   public function physical_code_type()
   {
      return view('backend.pages.physical_code_type.index');
   }

   public function information_type()
   {
      return view('backend.pages.information_type.index');
   }

   public function services()
   {
      return view('backend.pages.services.index');
   }

   public function actors()
   {
      return view('backend.pages.actors.index');
   }

   public function geolocalizations()
   {
      return view('backend.pages.article_content_maker.geolocalizations.index');
   }

   public function geolocalizations_show($id)
   {
      $article = MedicalArticle::findOrFail($id);

      return view('backend.pages.article_content_maker.geolocalizations.show')->withArticle($article);
   }

   public function services_exclusive()
   {
      return view('backend.pages.services_exclusive.index');
   }

   public function providers()
   {
      return view('backend.pages.providers.index');
   }

   public function parameters()
   {
      return view('backend.pages.parameters.index');
   }

   public function provider_services()
   {
      return view('backend.pages.provider_services.index');
   }

   public function provider_groups()
   {
      return view('backend.pages.provider_groups.index');
   }

   public function provider_groups_and_providers()
   {
      return view('backend.pages.provider_groups_and_providers.index');
   }

   public function workspace()
   {
      return view('backend.pages.workspace.index');
   }

   public function question_choices(){
      return view('backend.pages.questions.choices');
   }
   
   public function question_types_index() {
      return view('backend.pages.questions.question-types.index');
   }

   public function question_list_index() {
      return view('backend.pages.questions.question-list.index');
   }
   
   public function result_list_index(){
      return view('backend.pages.result.resultlist');

   }

   public function payment_plans(){
      return view('backend.pages.payment.plan');
   }

   public function procedure_list_index(){
      return view('backend.pages.procedure.procedure');

   }
}
