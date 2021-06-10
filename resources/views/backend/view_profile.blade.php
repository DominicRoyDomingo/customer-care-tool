@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.profiles.view'))

@section('breadcrumb-links')
@endsection

@push("after-styles")
<style>
    .local_inputs {
        display: none;
    }

    h5 {
        font-size: .85rem;
    }

    h6 {
        font-size: .75rem;
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-8">
                <h4 class="card-title mb-0">
                    {{ __('labels.backend.access.profiles.client_profile') }}
                </h4>
            </div>
            <div class="col-sm-4 text-right">
                <a href="../profiles?brand={{ request()->brand }}">
                    {{ __('labels.backend.access.profiles.view_profile.see_clients_list') }}
                </a>
            </div>
            <div class="col-sm-12">
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <h5 class="card-title mb-0">
                    {{ __('labels.backend.access.profiles.view_profile.full_name') }}: <u
                        class="" id="profile_full_name">{{ $profile->full_name }}</u>
                </h5>
                <h5 class="card-title mb-0">
                    {{ __('labels.backend.access.profiles.view_profile.nickname') }}: <u
                        class="" id="profile_nickname">{{ $profile->nickname }}</u>
                </h5>
                <h5 class="card-title mb-0">
                    {{ __('labels.backend.access.profiles.view_profile.primary_email') }}: <u
                        class="" id="profile_primary_email">{{ $profile->primary_email }}</u>
                </h5>
                <h5 class="card-title mb-0">
                    {{ __('labels.backend.access.profiles.view_profile.location') }}: <u
                        class="" id="profile_location_display">{{ $profile->location_display }}</u>
                </h5>
                <h5 class="card-title mb-0">
                    {{ __('labels.backend.access.profiles.view_profile.jobs') }}:
                    <ul>
                        @foreach($profile->jobs_display as $job_display)
                            <li>
                                <u>{{ $job_display }}</u>
                            </li>
                        @endforeach
                    </ul>
                </h5>
            </div>
            <div class="col-sm-4 text-right">
                <a href="#" data-id="{{ $profile->id }}" data-full_name="{{ $profile->full_name }}" id="edit-profile-btn">{{ __('labels.backend.access.profiles.view_profile.edit_profile') }}</a>    
            </div>
            <div class="col-sm-12">
                <hr>
            </div>
        </div>
        <!--row-->
    </div>
    <!--card-body-->
</div>
<!--card-->

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <h4 class="card-title mb-0">
                    {{ __('labels.backend.access.profiles.view_profile.notes') }}
                </h4>
            </div>
            <div class="col-sm-6 text-right">
                <a href="#" data-profile_id="{{ $profile->id }}" data-full_name="{{ $profile->full_name }}" id="add-note-btn">{{ __('labels.backend.access.profiles.view_profile.add_note') }}</a>
            </div>
            <div class="col-sm-12">    
                <hr>
            </div>
        </div>
        <div id="notes-container"></div>
        <!--row-->
    </div>
    <!--card-body-->
</div>
<!--card-->

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <h4 class="card-title mb-0">
                    {{ __('labels.backend.access.profiles.view_profile.contacts') }}
                </h4>
            </div>
            <div class="col-sm-6 text-right">
                <a href="#" data-profile_id="{{ $profile->id }}" data-full_name="{{ $profile->full_name }}" id="add-contact-btn">{{ __('labels.backend.access.profiles.view_profile.add_contact') }}</a>
            </div>
            <div class="col-sm-12">
                <hr>
            </div>
        </div>
        <div id="contacts-container"></div>
        <!--row-->
    </div>
    <!--card-body-->
</div>

<profile-page :profile_id="{{ $profile->id }}"></profile-page>

<!-- Modal -->
<div class="modal fade" id="editNoteModal" tabindex="-1" role="dialog" aria-labelledby="editNoteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editNoteModalLabel">{{ __('labels.backend.access.profiles.view_profile.note_form') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="note-div note-data">
                            <hr>
                            <div class="row">
                                <div class="col-md-3 text-left">
                                    <label
                                        for="date_requested">{{ __('labels.backend.access.profiles.forms.date_requested') }}</label>
                                </div>
                                <div class="col-md-9 text-center">
                                    <datepicker input-class="form-control bg-white" id="date_requested_picker"></datepicker>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-3 text-left">
                                    <label for="note">{{ __('labels.backend.access.profiles.forms.notes') }}</label>
                                </div>
                                <div class="col-md-9 text-center">
                                    <textarea id="note" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('buttons.general.close')</button>
                <button id="save-btn" data-action="add" type="button" class="btn btn-primary">
                    <i class="fas fa-save" id="save_icon"></i>
                    <i class="fas fa-spinner fa-spin" id="save_spinner" style="display:none;"></i>
                    @lang('buttons.general.save')
                </button>
            </div>
        </div>
    </div>
</div>


<add-notes-form 
:form_labels="{{json_encode(__('labels.backend.access.profiles.forms'))}}" 
:general_labels="{{json_encode(__('buttons.general'))}}" 
>
</add-notes-form>

<!-- Modal -->
<div class="modal fade" id="editContactModal" tabindex="-1" role="dialog" aria-labelledby="editContactModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editContactModalLabel">{{ __('labels.backend.access.profiles.view_profile.contact_form') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="contact-div contact-data" >
                            <div class="row">
                                <div class="col-md-3 text-left">
                                    <label>{{ __('labels.backend.access.profiles.forms.contact_type') }}</label>
                                </div>
                                <div class="col-md-9 text-center">
                                    <select  id="contact_type_id"   style="width:100%;" class="contact_types_selection" onchange="checkContactMatch()">
                                        @foreach($contact_types as $contact_type)
                                            <option value="{{ $contact_type->id }}">
                                                {{$contact_type->localized_name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-3 text-left">
                                    <label for="contact">{{ __('labels.backend.access.profiles.forms.contact_information') }}</label>
                                </div>
                                <div class="col-md-9 text-center">
                                    <input type="text" id="contact_info" class="form-control" onkeyup="checkContactMatch()">
                                </div>
                                <div class="col-md-12">
                                    <p class="text-primary text-left" style="display:none;" id="contact_check_loading">
                                        <i class="fas fa-spinner fa-spin"></i> {{ __('labels.backend.access.profiles.forms.contact_information') }}
                                    </p>
                                    <p class="text-danger text-left" style="display:none;" id="contact_check_match_found">
                                        <i class="fas fa-exclamation-triangle"></i> ERROR: Found profile with same contact information, client's full name is <u id="contact_check_matched_full_name"></u> 
                                    </p>
                                    <p class="text-success text-left" style="display:none;" id="contact_check_match_note_found">
                                        <i class="fas fa-check"></i> No profile found with the same contact information. 
                                    </p>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('buttons.general.close')</button>
                <button id="save-btn" data-action="add" type="button" class="btn btn-primary">
                    <i class="fas fa-save" id="save_icon"></i>
                    <i class="fas fa-spinner fa-spin" id="save_spinner" style="display:none;"></i>
                    @lang('buttons.general.save')
                </button>
            </div>
        </div>
    </div>
</div>

<add-contacts-form 
:form_labels="{{json_encode(__('labels.backend.access.profiles.forms'))}}" 
:general_labels="{{json_encode(__('buttons.general'))}}" 
:contact_types="{{json_encode($contact_types)}}"
>
</add-contacts-form>

<!-- Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">{{ __('labels.backend.access.profiles.forms.title') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-10">
                        <profile-form :form_labels="{{json_encode(__('labels.backend.access.profiles.forms'))}}"
                            :contact_types="{{json_encode($contact_types)}}" :brands="{{json_encode($brands)}}"
                            :countries="{{json_encode($countries)}}" :loaded_brand="{{json_encode($loaded_brand)}}">
                        </profile-form>
                    </div>
                    <div class="col-md-1">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    @lang('buttons.general.close')</button>
                <button id="save-btn" data-action="add" data-editing="profile" type="button" class="btn btn-primary">
                    <i class="fas fa-save" id="save_icon"></i>
                    <i class="fas fa-spinner fa-spin" id="save_spinner" style="display:none;"></i>
                    @lang('buttons.general.save')
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="contactTypeModal" tabindex="-1" role="dialog" aria-labelledby="contactTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="contactTypeModalLabel">Contact Type Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
              
            <div class="col-lg-12">
                <div class="form-group">
                    <div class="alert alert-danger" id="modal-error2" role="alert" style="display:none"></div>
                </div>
            </div>
            
              <div class="col-md-12">
                <div class="row">
                      <div class="col-md-3">
                          <label for="language" class="mt-2">@lang('menus.language-picker.language')</label>
                      </div>
                      <div class="col-md-9">
                          <select id="language" class="form-control" name="language">
                            <option value="en">English</option>
                            <option value="it">Italian</option>
                          </select>
                      </div>
                </div>
                <div class="row mt-1">
                        <div class="col-md-3">
                            <label for="type_name" class="mt-2">@lang('labels.backend.access.contact_types.forms.type_name')</label>
                        </div>
                      <div class="col-md-9">
                        <input type="text" name="type_name_en" id="type_name_en" class="form-control local_inputs en_inputs">
                        <input type="text" name="type_name_it" id="type_name_it" class="form-control local_inputs it_inputs">
                      </div>
                      <input type="hidden" name="contact_type_id" id="contact_type_id">
                </div>
              </div>
          </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button id="save-btn2" data-action="add" type="button" class="btn btn-primary">
                <i class="fas fa-save" id="save_icon2"></i>
                <i class="fas fa-spinner fa-spin" id="save_spinner2" style="display:none;"></i>
                Save changes
            </button>
        </div>
      </div>
    </div>
</div>


<!--card-->
@endsection


@push("after-scripts")
<script>
    let loaded_profile_id = '{{ $profile->id }}';
    let label_texts = {!! json_encode(__('labels.backend.access.profiles.view_profile')) !!};
    let add_notes_form_instance = null;
    let add_contacts_form_instance = null;
    let profile_form_instance = null;
    let profile_page_instance = null;
    let edit_note_datepicker = null;
    let locale = '{{strtolower(app()->getLocale())}}';

    function reset_form(click_notes_tab = true){
        //console.log(click_notes_tab);

        if(click_notes_tab == true)
            $("#notes-tab").trigger("click");
        

        profile_form_instance.surname = "";
        profile_form_instance.firstname =  "";
        profile_form_instance.middlename =  "";
        profile_form_instance.nickname =  "";
        profile_form_instance.primary_email =  "";
        profile_form_instance.notes = [];
        profile_form_instance.contacts = [];
        profile_form_instance.jobs = [];
        profile_form_instance.add_notes = false;
        profile_form_instance.add_contacts = false;

        //Matching states
        profile_form_instance.name_matching_state = "Default";
        profile_form_instance.name_match_found = false;
        profile_form_instance.name_matched_profile = {};
        profile_form_instance.email_matching_state = "Default";
        profile_form_instance.email_match_found = false;
        profile_form_instance.email_matched_profile = {};
        
        //profile_form_instance.$forceUpdate();
    }

    function reset_form2(){
        $(".local_inputs").val("");
    }

    function load_profile_info(data){
        $("#profile_full_name").html(data.full_name);
        $("#profile_nickname").html(data.nickname);
        $("#profile_primary_email").html(data.primary_email);
        $("#profile_location_display").html(data.location_display);
        $("#profile_profession_display").html(data.job_profession_display);
    }


    function initialize_form2(){
        $("." + locale + "_inputs").show();
        $("#language").val(locale).trigger("change");
    }

    function hideContactMatchTips() {
        $("#contact_check_loading").hide();
        $("#contact_check_match_found").hide();
        $("#contact_check_match_note_found").hide();
    }
    
    function checkContactMatch() {
        let ctype = $("#editContactModal #contact_type_id").val();
        let cinfo = $("#editContactModal #contact_info").val();

        let oinfo = $("#editContactModal #contact_info").data("old_info");
        let otype = $("#editContactModal #contact_info").data("old_type");
        
        //console.log("ctype");
        //console.log(ctype);
        //console.log("cinfo");
        //console.log(cinfo);
        if(ctype == otype && oinfo == cinfo){
            hideContactMatchTips();
            return;
        }

        if(ctype == "" || cinfo == ""){
            hideContactMatchTips();
            return;
        }

        let data = {
            contact_type_id: ctype,
            contact_info: cinfo
        }

        //console.log(data);
        let vm = this;

        $.ajax({
            type: "POST",
            url: "/admin/contact_matches",
            data: data,
            beforeSend: function(){
                $("#contact_check_loading").show();
            },
            success: function(data){
                hideContactMatchTips();
                
                if(data.length > 0){
                    $("#contact_check_match_found").show();
                    $("#contact_check_matched_full_name").text(data[0].profile.full_name);
                }
                else{
                    $("#contact_check_match_note_found").show();
                }
            },
            error: function(err){

                //console.log(err);
            }
        });
    }

    function note_row(data){
        let notes_data = JSON.stringify(data);
        //console.log(notes_data);

        let output = '\
            <div class="row">\
                <div class="col-sm-6">\
                    <h5>\
                        <b><i>' + label_texts.date_requested + ':</i> ' + data.date_requested_display + '</b>\
                    </h5>\
                </div>\
                <div class="col-sm-6 text-right">\
                    <a href="#" class="edit-note-btn" data-profile_id="' + loaded_profile_id + '" data-note=\'' + JSON.stringify(data) + '\'>' + label_texts.edit_note + '</a>\
                </div>\
                <div class="col-sm-12">\
                    <h5>\
                        ' + data.note_display + '\
                    </h5>\
                </div>\
                <div class="col-sm-12">\
                    <h6 class="text-right">\
                        ' + label_texts.notes_added_by + ': <i>' + data.added_by + '</i>, ' + data.date_added_display + '\
                    </h6>\
                </div>\
                <div class="col-sm-12">\
                    <h6 class="text-right">\
                        <hr>\
                    </h6>\
                </div>\
            </div>\
        ';

        return output;
    }

    function refreshNotes(){
        let type = "GET";
        let action_route = "../get_notes";
        
        $.ajax({
            type: type,
            url: action_route + "/" + loaded_profile_id,
            success: function(data){
                let notes = data;
                let notes_container_html = "";

                $("#notes-container").html(label_texts.no_notes);

                if(notes.length > 0){
                    notes.forEach(function(note){
                        notes_container_html += note_row(note);
                    });

                    $("#notes-container").html(notes_container_html);
                }
            }
        });
    }


    function contact_row(data){
        let contact_data = JSON.stringify(data);
        //console.log(contact_data);

        let output = '\
            <div class="row">\
                <div class="col-sm-6">\
                    <h5 class="card-title mb-0">\
                        ' + data.contact_type.localized_name + ':\
                    </h5>\
                </div>\
                <div class="col-sm-6 text-right">\
                    <a href="#" class="edit-contact-btn" data-contact=\'' + JSON.stringify(data) + '\'>' + label_texts.edit_contact + '</a>\
                </div>\
                <div class="col-sm-12">\
                    <h5 class="card-title mb-0 ml-5"><i class="">' + data.contact_info + '</i>\
                    </h5>\
                    <hr>\
                </div>\
            </div>\
        ';

        return output;
    }

    function refreshContacts(){
        let type = "GET";
        let action_route = "../get_contacts";
        
        $.ajax({
            type: type,
            url: action_route + "/" + loaded_profile_id,
            success: function(data){
                let contacts = data;
                let contacts_container_html = "";

                $("#contacts-container").html(label_texts.no_contacts);

                if(contacts.length > 0){
                    contacts.forEach(function(contact){
                        contacts_container_html += contact_row(contact);
                    });

                    $("#contacts-container").html(contacts_container_html);
                }
            }
        });
    }

    $(function(){
        let app_children = app.$children;
        
        app_children.forEach(function(child){
            let child_name = child.$options.name;
            //console.log(child_name);
            if(child_name == "add_notes_form"){
                add_notes_form_instance = child;
            }
            else if(child_name == "add_contacts_form"){
                add_contacts_form_instance = child;
            }
            else if(child_name == "datepicker"){
                edit_note_datepicker = child;
            }
            else if(child_name == "profile_form"){
                profile_form_instance = child;
            }
            else if(child_name == "profile_page"){
                profile_page_instance = child;
            }
            
            
            
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $("#profileModal #save-btn").on("click", function(){
            let route = "/admin/profiles";
            let action = $(this).data("action");
            let editing = $(this).data("editing");
            let type = "POST";
            let action_route = route;

            if(action != "add"){
                type = "PUT";
                action_route += "/" + $("#profile_id").val();
            }

            if(!profile_form_instance.validateForm()){
                return;
            }

            let notes = [];

            if(profile_form_instance.add_notes)
                notes = profile_form_instance.notes;

            let contacts = [];

            if(profile_form_instance.add_contacts)
                contacts = profile_form_instance.contacts;
                
            let brands = profile_form_instance.selected_brands;
            
            let jobs = [];

            let data = {
                "surname": profile_form_instance.surname,
                "firstname": profile_form_instance.firstname,
                "middlename": profile_form_instance.middlename,
                "nickname": profile_form_instance.nickname,
                "primary_email": profile_form_instance.primary_email,
                "notes": notes,
                "contacts": contacts,
                "brands": brands,
                "location": {
                    "country_id": profile_form_instance.selected_country,
                    "province_id": profile_form_instance.selected_province,
                    "city_id": profile_form_instance.selected_city,
                },
                "jobs": profile_form_instance.jobs,
            };

            console.log(data);

            let profile_form_instance_children = profile_form_instance.$children;
            
            let contacts_has_matches = false;

            profile_form_instance_children.forEach(function(children){
                if(children.$options.name == "contact_div"){
                    if(children.$data.match_found)
                        contacts_has_matches = true;
                }
            });

            if(contacts_has_matches || profile_form_instance.email_match_found){
                $.confirm({
                    title: 'Error',
                    content: 'You cannot use the same contact info for different profiles.',
                    type: 'alert',
                    typeAnimated: true,
                    buttons: {
                        tryAgain: {
                            text: 'Confirm',
                            btnClass: 'btn-danger text-white',
                            action: function(){

                            }
                        }
                    }
                });

                return;
            }

            //console.log(data);
            if(profile_form_instance.name_match_found){
                $.confirm({
                    title: 'Confirm Action',
                    content: 'The system has detected that you have entered the same first and last name to an existing record. However if your know this record is different you may wish to continue',
                    type: 'warning',
                    typeAnimated: true,
                    buttons: {
                        tryAgain: {
                            text: 'Proceed',
                            btnClass: 'btn-warning text-white',
                            action: function(){
                                $.ajax({
                                    type: type,
                                    url: action_route,
                                    data: data,
                                    beforeSend: function(){
                                        startLoading("save-btn", "save_spinner", "save_icon");
                                    },
                                    success: function(data){
                                        endLoading("save-btn", "save_spinner", "save_icon");
                                        profile_form_instance.makeToast("warning"," @lang('strings.backend.toast.title.record_updated')"," @lang('strings.backend.profiles.updated') " + data.id + " @lang('strings.backend.profiles.updated2')");
                                        load_profile_info(data);
                                        refreshNotes();
                                        refreshContacts();
                                        $('#profileModal').modal('hide');
                                    },
                                    error: function(err){
                                        endLoading("save-btn", "save_spinner", "save_icon");
                                        //console.log(err);
                                        //Place update code here
                                        $("#modal-error").html("Something went wrong").slideDown();
                                        
                                        setTimeout(function(){
                                            $("#modal-error").fadeOut();
                                        }, 3000);
                                    }
                                });
                            }
                        },
                        cancel: function () {
                        }
                    }
                });

            }
            else{
                $.ajax({
                    type: type,
                    url: action_route,
                    data: data,
                    beforeSend: function(){
                        startLoading("save-btn", "save_spinner", "save_icon");
                    },
                    success: function(data){
                        endLoading("save-btn", "save_spinner", "save_icon");
                        
                        profile_form_instance.makeToast("warning"," @lang('strings.backend.toast.title.record_updated')"," @lang('strings.backend.profiles.updated') " + data.id + " @lang('strings.backend.profiles.updated2')");
                        load_profile_info(data);
                        reset_form(true);
                        refreshNotes();
                        refreshContacts();

                        $('#profileModal').modal('hide');
                    },
                    error: function(err){
                        endLoading("save-btn", "save_spinner", "save_icon");
                        
                    }
                });
            }
        });

        $("#edit-profile-btn").on("click", function(e){
            e.preventDefault();
            console.log("Test");
            $("#notes-tab").trigger("click");
            profile_form_instance.form_state = "Profile";
            reset_form(true);
            
            $("#modal-error").hide();

            let route = "/admin/profiles";
            let id = $(this).data("id");
            let type = "GET";
            let action_route = route;
            $("#profileModal #save-btn").data("action", "edit");
            $("#profileModal #save-btn").data("editing", "profile");

            $.ajax({
                type: type,
                url: action_route + "/" + id,
                beforeSend: function(){
                    startLoading("edit-btn-" + id, "edit-spinner-" + id, "edit-icon-" + id);
                },
                success: function(data){
                    endLoading("edit-btn-" + id, "edit-spinner-" + id, "edit-icon-" + id);
                    
                    profile_form_instance.surname = data.surname;
                    profile_form_instance.firstname = data.firstname;
                    profile_form_instance.middlename = data.middlename;
                    profile_form_instance.nickname = data.nickname;
                    profile_form_instance.primary_email = data.primary_email;


                    if(data.client_location != null){
                        profile_form_instance.selected_country = data.client_location.world_countries_id;
                        profile_form_instance.loadProvinces();
                        profile_form_instance.selected_province = data.client_location.world_provinces_id;
                        profile_form_instance.loadCities();
                        profile_form_instance.selected_city = data.client_location.world_cities_id;
                    }
                    else{
                        profile_form_instance.selected_country = null;
                        profile_form_instance.selected_province = null;
                        profile_form_instance.selected_city = null;
                    }
                    
                    profile_form_instance.load_editing_behavior();
                    let notes = data.notes;
                    profile_form_instance.notes = notes;

                    //console.log(notes);
                    if(notes.length > 0){
                        profile_form_instance.add_notes = true;
                    }

                    let contacts = data.contacts;

                    contacts.forEach(function(contact){
                        contact.recompute = true;
                    });

                    profile_form_instance.contacts = contacts;
                    //console.log(contacts);
                    if(contacts.length > 0){
                        profile_form_instance.add_contacts = true;
                    }
                    //profile_form_instance.$forceUpdate();

                    if(data.jobs != undefined){
                        if(data.jobs.length > 0){
                            let jobs = [];
                            
                            data.jobs.forEach(function(job){
                                console.log("job");
                                console.log(job);
                                let job_object = {
                                    id: job.id,
                                    job_category_id: job.job_category_id,
                                    job_profession_id: job.job_profession_id,
                                    job_description_id: job.job_description_id
                                };

                                console.log("job_object");
                                console.log(job_object);
                                jobs.push(job_object);
                            });

                            profile_form_instance.jobs = jobs;
                        }
                    }

                    setTimeout(function(){
                        if(profile_form_instance.jobs.length > 0){
                            profile_form_instance.syncJobs();
                        }
                    }, 10);
                    
                    $("#profile_id").val(data.id);
                    $("#profileModal").modal("show");
                },
                error: function(err){
                    endLoading("edit-btn-" + id, "edit-spinner-" + id, "edit-icon-" + id);
                    //console.log(err);
                    $("#modal-error").html("Something went wrong").slideDown();
                    
                    setTimeout(function(){
                        $("#modal-error").fadeOut();
                    }, 3000);
                }
            });
        });

        
        $("#add-note-btn").on("click", function(e){
            e.preventDefault();
            $("#addNoteModal").modal("show");
            let profile_id = $(this).data("profile_id");
            let full_name = $(this).data("full_name");

            add_notes_form_instance.profile_id = profile_id;
            add_notes_form_instance.full_name = full_name;
            add_notes_form_instance.notes = [];
            add_notes_form_instance.addNote();
        });

        
        $("#add-contact-btn").on("click", function(e){
            e.preventDefault();
            $("#addContactModal").modal("show");
            let profile_id = $(this).data("profile_id");
            let full_name = $(this).data("full_name");

            add_contacts_form_instance.profile_id = profile_id;
            add_contacts_form_instance.full_name = full_name;
            add_contacts_form_instance.contacts = [];
            add_contacts_form_instance.addContact();
        });

        $(document).on("click", ".edit-note-btn", function(e){
            e.preventDefault();
            $("#editNoteModal").modal("show");
            let note = $(this).data("note");
            let profile_id = $(this).data("profile_id");
            $("#editNoteModal #note").val(note.notes);
            edit_note_datepicker.selectedDate = new Date(note.date_requested);
            $("#editNoteModal #save-btn").data("profile_id", profile_id);
            $("#editNoteModal #save-btn").data("note", note);
            //console.log(note);
        });

        $("#editNoteModal #save-btn").on("click", function(){
            let profile_id = $(this).data("profile_id");
            let note = $(this).data("note");
            let previous_modal = $(this).data("previous_modal");
            let route = "../update_note/" + profile_id;
            let type = "POST";

            note.date_requested = formatDateToYMD(edit_note_datepicker.selectedDate);
            note.notes = $("#editNoteModal #note").val();
            //console.log(note);

            $.ajax({
                type: type,
                url: route,
                data: note,
                beforeSend: function() {
                    startLoading("editNoteModal #save-btn", "editNoteModal #save_spinner", "editNoteModal #save_icon");
                },
                success: function(data){
                    endLoading("editNoteModal #save-btn", "editNoteModal #save_spinner", "editNoteModal #save_icon");

                    refreshNotes();
                    $("#editNoteModal #date_requested").val("");
                    $("#editNoteModal #note").val("");


                    $('#editNoteModal').modal('hide');
                },
                error: function(err){
                    endLoading("editNoteModal #save-btn", "editNoteModal #save_spinner", "editNoteModal #save_icon");
                }
            });

        });

        $(document).on("click", ".edit-contact-btn", function(e){
            e.preventDefault();
            $("#editContactModal").modal("show");
            let contact = $(this).data("contact");
            
            $("#editContactModal #contact_type_id").val(contact.contact_type_id).trigger("change");
            $("#editContactModal #contact_info").val(contact.contact_info);
            $("#editContactModal #contact_info").data("old_info", contact.contact_info);
            $("#editContactModal #contact_info").data("old_type", contact.contact_type_id);

            $("#editContactModal #save-btn").data("contact", contact);
            hideContactMatchTips();
            //console.log(contact);
        });

        $("#editContactModal #save-btn").on("click", function(){
            if($("#contact_check_match_note_found").is(":visible")){
                $.confirm({
                    title: 'Error',
                    content: 'You cannot use the same contact info for different profiles.',
                    type: 'alert',
                    typeAnimated: true,
                    buttons: {
                        tryAgain: {
                            text: 'Confirm',
                            btnClass: 'btn-danger text-white',
                            action: function(){

                            }
                        }
                    }
                });
                return;
            }

            let profile_id = $(this).data("profile_id");
            let contact = $(this).data("contact");

            let route = "../contacts/" + contact.id;
            let type = "PUT";

            let data = {
                contact_type_id:  $("#editContactModal #contact_type_id").val(),
                contact_info: $("#editContactModal #contact_info").val()
            }

            $.ajax({
                type: type,
                url: route,
                data: data,
                beforeSend: function(){
                    startLoading("editContactModal #save-btn", "editContactModal #save_spinner", "editContactModal #save_icon");
                },
                success: function(data){
                    endLoading("editContactModal #save-btn", "editContactModal #save_spinner", "editContactModal #save_icon");
                    refreshContacts();
                    hideContactMatchTips();
                    $('#editContactModal').modal('hide');
                },
                error: function(err){
                    endLoading("editContactModal #save-btn", "editContactModal #save_spinner", "editContactModal #save_icon");
                }
            });

        });


        //Contact Types
                
        $("#save-btn2").on("click", function(){
            $("#modal-error2").hide();
            let route = "/admin/contact_types";
            let action = $(this).data("action");
            let previous_modal = $(this).data("previous_modal");
            let type = "POST";
            let action_route = route;

            if(action != "add"){
                type = "PUT";
                action_route += "/" + $("#contact_type_id").val();
            }

            let data = {
                "type_name": {
                    "en": $("#type_name_en").val(),
                    "it": $("#type_name_it").val(),
                }
            };

            
            $.ajax({
                type: type,
                url: action_route,
                data: data,
                beforeSend: function(){
                    startLoading("save-btn2", "save_spinner2", "save_icon2");
                },
                success: function(data){
                    $("#modal-error").hide();
                    endLoading("save-btn2", "save_spinner2", "save_icon2");
                    let type_name = JSON.parse(data.type_name);
                    if(locale == "en")
                        type_name = type_name.en;
                    else if (locale == "it")
                        type_name = type_name.it;
                    
                    if(action == "add"){
                        //console.log(data);
                        show_alert(type_name + " @lang('strings.backend.contact_types.added')", "success", "fas fa fa-info-circle");
                        
                        add_contacts_form_instance.contact_types.push(data);
                        
                        add_contacts_form_instance.$children.forEach(function(kid){
                            if(kid.$options.name == "contact_div"){
                                kid.$forceUpdate();
                            }
                        });

                        var newOption = new Option(data.localized_name, data.id, false, false);
                        $('#editContactModal #contact_type_id').append(newOption).trigger('change');

                    }
                    
                    reset_form2();

                    $('#contactTypeModal').modal('hide');
                },
                error: function(err){
                    endLoading("save-btn2", "save_spinner2", "save_icon2");
                    //console.log(err);
                    $("#modal-error2").html("Something went wrong").slideDown();
                    setTimeout(function(){
                        $("#modal-error2").fadeOut();
                    }, 3000);
                }
            });
        });

        $(document).on("click", ".addContactTypeBtn", function(){
            let select_id = $(this).data("select_id");
            let previous_modal = $(this).data("previous_modal");
            //console.log(previous_modal);

            $("#save-btn2").data("previous_modal", previous_modal);
            $(select_id).select2("close");

            if(previous_modal == "#editContactModal") {
                $('#editContactModal').modal('hide');
            }
            else{
                $('#addContactModal').modal('hide');
            }
        });

        $('#contactTypeModal').on('hidden.bs.modal', function () {
            let previous_modal = $("#save-btn2").data("previous_modal");
            if(previous_modal == "#editContactModal") {
                $('#editContactModal').modal('show');
            }
            else{
                $('#addContactModal').modal('show');
            }
        });

        $('#editContactModal #contact_type_id')
        .select2({
            placeholder: "{{ __('labels.backend.access.profiles.forms.select_contact_type') }}"
        })
        .on('select2:open', () => {
            $(".select2-results:not(:has(a))").prepend('<a href="#" class="addContactTypeBtn" data-toggle="modal" data-target="#contactTypeModal" data-previous_modal="#editContactModal" data-select_id="#editContactModal #contact_type_id" style="padding: 6px;height: 20px;display: inline-table;">{{ __('labels.backend.access.profiles.forms.create_new_contact_type') }}</a>');
        });
        
        refreshNotes();
        refreshContacts();
        initialize_form2();
    });
</script>
@endpush