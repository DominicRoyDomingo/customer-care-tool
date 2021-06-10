@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.profiles.management'))

@push("after-styles")
<style>
    .local_inputs {
        display: none;
    }

    .contact-display h5,
    .note-display h5 {
        font-size: .85rem;
    }

    .contact-display h6,
    .note-display h6 {
        font-size: .75rem;
    }
</style>
@endpush
@section('content')

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-5">
                <h4 class="card-title mb-0">
                    {{ __('labels.backend.access.profiles.management') }} <small
                        class="text-muted">{{ __('labels.backend.access.profiles.active') }}</small>
                </h4>
                <h5 class="mt-2">
                    @if(request()->brand == "none")
                        No Brand
                    @else
                        {{ ucfirst(request()->brand) }}
                    @endif
                </h5>
            </div>
            <!--col-->

            <div class="col-sm-7">
                <button id="add-btn" class="btn btn-success ml-1 float-right" data-toggle="tooltip"
                    title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></button>
            </div>
            <!--col-->
        </div>
        <!--row-->

        <div class="row mt-4">
            <div class="col table-responsive">
                <table class="table table-striped" id="table" style="width: 100% !important;">
                    <thead>
                        <tr>
                            <th>@lang('labels.backend.access.profiles.table.full_name')</th>
                            <th>@lang('labels.backend.access.profiles.table.for_what_brand')</th>
                            <th>@lang('labels.general.actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profiles as $profile)
                        <tr id="profile_{{ $profile->id }}">
                            <td><a
                                    href="view_profile/{{ $profile->id }}?brand={{ request()->brand }}">{{ $profile->full_name }}</a>
                            </td>
                            <td>{{ $profile->brand_names }}</td>
                            <td class="btn-td">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                        id="profile-actions-{{ $profile->id }}" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        More Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="profile-actions-{{ $profile->id }}">
                                        <a class="dropdown-item client-engagement-btn" href="#" data-id="{{ $profile->id }}" data-profile_name="{{ $profile->profile_name }}"
                                            id="client-engagement-btn-{{ $profile->id }}">
                                            <i class="fas fa-thumbs-up text-dark"
                                                id="client-engagement-icon-{{ $profile->id }}"></i>
                                            <i class="fas fa-spinner fa-spin" id="client-engagement-spinner-{{ $profile->id }}"
                                                style="display:none;"></i>
                                            @lang('buttons.profiles.add_client_engagements')
                                        </a>
                                        <a class="dropdown-item brand-btn" href="#" data-id="{{ $profile->id }}" data-profile_name="{{ $profile->profile_name }}"
                                            id="brand-btn-{{ $profile->id }}">
                                            <i class="fas fa-briefcase text-dark"
                                                id="brand-icon-{{ $profile->id }}"></i>
                                            <i class="fas fa-spinner fa-spin" id="brand-spinner-{{ $profile->id }}"
                                                style="display:none;"></i>
                                            @lang('buttons.profiles.link_to_brand')
                                        </a>
                                        <a class="dropdown-item edit-btn" href="#" data-id="{{ $profile->id }}" data-profile_name="{{ $profile->profile_name }}"
                                            id="edit-btn-{{ $profile->id }}">
                                            <i class="fas fa-edit text-warning" id="edit-icon-{{ $profile->id }}"></i>
                                            <i class="fas fa-spinner fa-spin" id="edit-spinner-{{ $profile->id }}"
                                                style="display:none;"></i>
                                            @lang('buttons.general.crud.edit')
                                        </a>
                                        <a class="dropdown-item delete-btn" href="#" data-id="{{ $profile->id }}"
                                            data-full_name="{{ $profile->full_name }}"
                                            id="delete-btn-{{ $profile->id }}">
                                            <i class="fas fa-trash text-danger" id="delete-icon-{{ $profile->id }}"></i>
                                            <i class="fas fa-spinner fa-spin" id="delete-spinner-{{ $profile->id }}"
                                                style="display:none;"></i>
                                            @lang('buttons.general.crud.delete')
                                        </a>
                                        <a class="dropdown-item add-contact-btn" href="#" data-id="{{ $profile->id }}" data-profile_name="{{ $profile->profile_name }}"
                                            id="add-contact-btn-{{ $profile->id }}">
                                            <i class="fas fa-address-book text-primary"
                                                id="add-contact-icon-{{ $profile->id }}"></i>
                                            <i class="fas fa-spinner fa-spin"
                                                id="add-contact-spinner-{{ $profile->id }}" style="display:none;"></i>
                                            @lang('buttons.profiles.add_contact')
                                        </a>
                                        <a class="dropdown-item add-notes-btn" href="#" data-id="{{ $profile->id }}" data-profile_name="{{ $profile->profile_name }}"
                                            id="add-notes-btn-{{ $profile->id }}">
                                            <i class="fas fa-sticky-note text-primary"
                                                id="add-notes-icon-{{ $profile->id }}"></i>
                                            <i class="fas fa-spinner fa-spin" id="add-notes-spinner-{{ $profile->id }}"
                                                style="display:none;"></i>
                                            @lang('buttons.profiles.add_notes')
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none">{{ $profile->country_display }}</td>
                            <td class="d-none">{{ $profile->province_display }}</td>
                            <td class="d-none">{{ $profile->city_display }}</td>
                            <td class="d-none">{{ $profile->job_category_display }}</td>
                            <td class="d-none">{{ $profile->job_profession_display }}</td>
                            <td class="d-none">{{ $profile->job_description_display }}</td>
                            <td class="d-none">{{ $profile->primary_email }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!--col-->
        </div>
        <!--row-->
    </div>
    <!--card-body-->
</div>
<!--card-->

<!-- Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">
                    <span id="profileModalLabelMainText"></span><br class="profileModalLabelExtra" style="display:none">
                    <span class="profileModalLabelExtra" style="font-size:80%; display:none" id="profileModalLabelProfileName"></span>
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
<div class="modal fade" id="contactTypeModal" tabindex="-1" role="dialog" aria-labelledby="contactTypeModalLabel"
    aria-hidden="true">
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
                                <label for="type_name"
                                    class="mt-2">@lang('labels.backend.access.contact_types.forms.type_name')</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="type_name_en" id="type_name_en"
                                    class="form-control local_inputs en_inputs">
                                <input type="text" name="type_name_it" id="type_name_it"
                                    class="form-control local_inputs it_inputs">
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

<!-- Modal -->
<div class="modal fade" id="brandModal" tabindex="-1" role="dialog" aria-labelledby="brandModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandModalLabel">Link To Brand
                    <br><span style="font-size:80%;" id="brandModalLabelProfileName"></span>
                </h5>
                <h6 class="profile_name" style="display:none;"></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="language"
                                    class="mt-2">{{ __('labels.backend.access.profiles.forms.brands') }}</label>
                            </div>
                            <div class="col-md-12">
                                <select id="brands" class="form-control" name="language" multiple style="width:100%;">
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="brands_profile_id" id="brands_profile_id">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="save-brands-btn" data-action="add" type="button" class="btn btn-primary">
                    <i class="fas fa-save" id="save_brands_icon2"></i>
                    <i class="fas fa-spinner fa-spin" id="save_brands_spinner2" style="display:none;"></i>
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="newBrandModal" tabindex="-1" role="dialog" aria-labelledby="newBrandModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newBrandModalLabel">@lang('labels.backend.access.brands.forms.title')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="name"
                                    class="mt-2">@lang('labels.backend.access.brands.forms.brand_name')</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="name" id="name" class="form-control">
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-md-3">
                                <label for="website"
                                    class="mt-2">@lang('labels.backend.access.brands.forms.website')</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="website" id="website" class="form-control">
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-md-3">
                                <label for="logo" class="mt-2">@lang('labels.backend.access.brands.forms.logo')</label>
                            </div>
                            <div class="col-md-9">
                                <input type="file" name="logo" id="logo" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="save-new-brand-btn" data-action="add" type="button" class="btn btn-primary">
                    <i class="fas fa-save" id="save_new_brand_icon"></i>
                    <i class="fas fa-spinner fa-spin" id="save_new_brand_spinner" style="display:none;"></i>
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="addProvinceModal" tabindex="-1" role="dialog" aria-labelledby="addProvinceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProvinceModalLabel">
                    @lang('labels.backend.access.locations.provinces.add_province')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="name"
                                    class="mt-2">@lang('labels.backend.access.locations.provinces.province_name')</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="province_name" id="province_name" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="save-new-province-btn" data-action="add" type="button" class="btn btn-primary">
                    <i class="fas fa-save" id="save_new_province_icon"></i>
                    <i class="fas fa-spinner fa-spin" id="save_new_province_spinner" style="display:none;"></i>
                    @lang('buttons.general.save')
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addCityModal" tabindex="-1" role="dialog" aria-labelledby="addCityModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCityModalLabel">@lang('labels.backend.access.locations.cities.add_city')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="name"
                                    class="mt-2">@lang('labels.backend.access.locations.cities.city_name')</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="city_name" id="city_name" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="save-new-city-btn" data-action="add" type="button" class="btn btn-primary">
                    <i class="fas fa-save" id="save_new_city_icon"></i>
                    <i class="fas fa-spinner fa-spin" id="save_new_city_spinner" style="display:none;"></i>
                    @lang('buttons.general.save')
                </button>
            </div>
        </div>
    </div>
</div>

<profiles-page></profiles-page>
@endsection

@push("after-styles")
<style>
body {
  height: 100vh;
  overflow-y: hidden;
  padding-right: 15px; /* Avoid width reflow */
}
</style>
@endpush

@push("after-scripts")
<script>
    let table = null;
    let profile_modal_labels = [
        '@lang('labels.backend.access.profiles.forms.add_title')',      //Add title
        '@lang('labels.backend.access.profiles.forms.edit_title')',     //Edit title
        '@lang('labels.backend.access.profiles.forms.add_notes')',     //Add Note
        '@lang('labels.backend.access.profiles.forms.add_contacts')'     //Add Contacts
    ]
    let edit_tooltip = '@lang('buttons.general.crud.edit')';
    let delete_tooltip = '@lang('buttons.general.crud.delete')';
    let add_contact_tooltip = '@lang('buttons.profiles.add_contact')';
    let add_notes_tooltip = '@lang('buttons.profiles.add_notes')';
    let link_to_brand_tooltip = '@lang('buttons.profiles.link_to_brand')';
    let add_new_brand_tooltip = '@lang('buttons.profiles.add_new_brand')';
    let add_client_engagements_tooltip = '@lang('buttons.profiles.add_client_engagements')';
    let tableIsEmpty = {{ (count($profiles) == 0) ? 1 : 0}};
    let locale = '{{strtolower(app()->getLocale())}}';
    let profile_form_instance = null;
    let profiles_page_instance = null;
    let loaded_brand = '{{ $loaded_brand->id }}';
    let loaded_brand_name = '{{ 
        (request()->brand == "none") ? "No Brand" : ucfirst(request()->brand)
    }}'
    let no_brands_text = '@lang('menus.backend.sidebar.no_brand')';
    

    function table_row(data){
        if(data.country_display == null){
            data.country_display = "";
        }
        if(data.province_display == null){
            data.province_display = "";
        }
        if(data.city_display == null){
            data.city_display = "";
        }

        if(data.job_category_display == null){
            data.job_category_display = "";
        }
        if(data.job_profession_display == null){
            data.job_profession_display = "";
        }
        if(data.job_description_display == null){
            data.job_description_display = "";
        }
        
        var output = {
                "0": '<a href="view_profile/' + data.id + '?brand=' + loaded_brand_name + '">' + data.full_name + '</a>',
                "1": data.brand_names,
                "2": '\
                    <div class="dropdown">\
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="profile-actions-' + data.id + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\
                            More Actions\
                        </button>\
                        <div class="dropdown-menu" aria-labelledby="profile-actions-' + data.id + '">\
                            <a class="dropdown-item client-engagement-btn" href="#"  data-id="' + data.id + '" id="client-engagement-btn-' + data.id + '" data-profile_name="' + data.profile_name + '">\
                                <i class="fas fa-thumbs-up text-dark" id="client-engagement-icon-' + data.id + '"></i>\
                                <i class="fas fa-spinner fa-spin" id="client-engagement-spinner-' + data.id + '"\
                                    style="display:none;"></i>\
                                ' + add_client_engagements_tooltip + '\
                            </a>\
                            <a class="dropdown-item brand-btn" href="#"  data-id="' + data.id + '" id="brand-btn-' + data.id + '"  data-profile_name="' + data.profile_name + '">\
                                <i class="fas fa-briefcase text-dark" id="brand-icon-' + data.id + '"></i>\
                                <i class="fas fa-spinner fa-spin" id="brand-spinner-' + data.id + '"\
                                    style="display:none;"></i>\
                                ' + link_to_brand_tooltip + '\
                            </a>\
                            <a class="dropdown-item edit-btn" href="#" data-id="' + data.id + '" id="edit-btn-' + data.id + '"  data-profile_name="' + data.profile_name + '">\
                                <i class="fas fa-edit text-warning" id="edit-icon-' + data.id + '"></i>\
                                <i class="fas fa-spinner fa-spin" id="edit-spinner-' + data.id + '"\
                                    style="display:none;"></i>\
                                    ' + edit_tooltip + '\
                            </a>\
                            <a class="dropdown-item delete-btn" href="#"data-id="' + data.id + '"\
                                data-full_name="' + data.full_name + '"\
                                id="delete-btn-' + data.id + '">\
                                <i class="fas fa-trash text-danger" id="delete-icon-' + data.id + '"></i>\
                                <i class="fas fa-spinner fa-spin" id="delete-spinner-' + data.id + '"\
                                    style="display:none;"></i>\
                                    ' + delete_tooltip + '\
                            </a>\
                            <a class="dropdown-item add-contact-btn" href="#" data-id="' + data.id + '" id="add-contact-btn-' + data.id + '"  data-profile_name="' + data.profile_name + '">\
                                <i class="fas fa-address-book text-primary" id="add-contact-icon-' + data.id + '"></i>\
                                <i class="fas fa-spinner fa-spin" id="add-contact-spinner-' + data.id + '"\
                                    style="display:none;"></i>\
                                    ' + add_contact_tooltip + '\
                            </a>\
                            <a class="dropdown-item add-notes-btn" href="#" data-id="' + data.id + '" id="add-notes-btn-' + data.id + '"  data-profile_name="' + data.profile_name + '">\
                                <i class="fas fa-sticky-note text-primary" id="add-notes-icon-' + data.id + '"></i>\
                                <i class="fas fa-spinner fa-spin" id="add-notes-spinner-' + data.id + '"\
                                    style="display:none;"></i>\
                                    ' + add_notes_tooltip + '\
                            </a>\
                        </div>\
                    </div>\
                ',
                "3": data.country_display,
                "4": data.province_display,
                "5": data.city_display,
                "6": data.job_category_display,
                "7": data.job_profession_display,
                "8": data.job_description_display,
                "9": data.primary_email,
            };

        return output;
    }
    function reset_new_brand_form(){
        $("#name").val("");
        $("#website").val("");
        $("#logo").val("");
    }

    function reset_new_province_form(){
        $("#addProvinceModal #province_name").val("");        
    }

    function reset_new_city_form(){
        $("#addCityModal #city_name").val("");
    }

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
        profile_form_instance.selected_brands = [];
        profile_form_instance.form.client_engagements = [];
        profile_form_instance.resetLocation();
        profile_form_instance.load_brand();

        profile_form_instance.add_notes = false;
        profile_form_instance.add_contacts = false;
        profile_form_instance.add_jobs = false;
        profile_form_instance.add_engagements = false;

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

    function initialize_form2(){
        $("." + locale + "_inputs").show();
        $("#language").val(locale).trigger("change");
    }
    
    $(function(){
        
        let app_children = app.$children;
        
        app_children.forEach(function(child){
            let child_name = child.$options.name;
            //console.log(child_name);
            if(child_name == "profile_form"){
                profile_form_instance = child;
            }
            else if(child_name == "profiles_page"){
                profiles_page_instance = child;
            }
        });


        table = $('#table').DataTable({
                "columnDefs": [
                    { 
                        "targets": 0,
                        "width": '45%',
                        "className": 'text-left',
                    },
                    {
                        "targets": 1,
                        "width": '40%',
                        "className": 'text-center',
                    },
                    {
                        "targets": 2,
                        "width": '15%',
                        "className": 'text-center',
                        "orderable": false
                    },
                    {
                        "targets": 3,
                        "visible": false,
                        "orderable": false
                    },
                    {
                        "targets": 4,
                        "visible": false,
                        "orderable": false
                    },
                    {
                        "targets": 5,
                        "visible": false,
                        "orderable": false
                    },
                    {
                        "targets": 6,
                        "visible": false,
                        "orderable": false
                    },
                    {
                        "targets": 7,
                        "visible": false,
                        "orderable": false
                    },
                    {
                        "targets": 8,
                        "visible": false,
                        "orderable": false
                    },
                    {
                        "targets": 9,
                        "visible": false,
                        "orderable": false
                    },
                ]
            });


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#add-btn").on("click", function(){
            $(".profileModalLabelExtra").hide();
            $("#profileModalLabelMainText").text(profile_modal_labels[0] + loaded_brand_name);
            profile_form_instance.form_state = "Profile";
            profile_form_instance.load_adding_behavior();
            $("#save-btn").data("editing", "profile");
            reset_form(true);
            $("#profileModal").modal("show");
            $("#save-btn").data("action", "add");
        });

        $(document).on("click", ".edit-btn", function(){
            let profile_name = $(this).data("profile_name");
            $(".profileModalLabelExtra").hide();
            $("#profileModalLabelMainText").text(profile_modal_labels[1] + profile_name);

            $("#notes-tab").trigger("click");
            profile_form_instance.form_state = "Profile";
            reset_form(true);
            

            let route = "profiles";
            let id = $(this).data("id");
            let type = "GET";
            let action_route = route;
            $("#save-btn").data("action", "edit");
            $("#save-btn").data("editing", "profile");

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
                            profile_form_instance.add_jobs = true;
                            
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

                            setTimeout(function() {
                                profile_form_instance.syncJobs();
                            }, 100);
                        }
                    }

                    let client_engagements = data.client_engagements;

                    profile_form_instance.form.client_engagements = client_engagements;
                    //console.log(contacts);
                    if(client_engagements.length > 0){
                        profile_form_instance.add_engagements = true;
                    }
                    
                    $("#profile_id").val(data.id);
                    $("#profileModal").modal("show");
                },
                error: function(err){
                    endLoading("edit-btn-" + id, "edit-spinner-" + id, "edit-icon-" + id);
                    
                    profile_form_instance.makeToast("danger"," @lang('strings.backend.toast.title.error')", "@lang('strings.backend.general.something_went_wrong') ");
                    
                }
            });
        });

        $(document).on("click", ".delete-btn", function(){
            let route = "profiles";
            let id = $(this).data("id");
            let full_name = $(this).data("full_name");
            let type = "DELETE";
            let action_route = route;
            $.confirm({
                title: 'Confirm Action',
                content: 'You are about to delete the profile of \'' + full_name + "'",
                type: 'red',
                typeAnimated: true,
                buttons: {
                    tryAgain: {
                        text: 'Confirm',
                        btnClass: 'btn-red',
                        action: function(){              
                            $.ajax({
                                type: type,
                                url: action_route + "/" + id,
                                beforeSend: function(){
                                    startLoading("delete-btn-" + id, "delete-spinner-" + id, "delete-icon-" + id);
                                },
                                success: function(data){
                                    endLoading("delete-btn-" + id, "delete-spinner-" + id, "delete-icon-" + id);
                                    profile_form_instance.makeToast("danger", " @lang('strings.backend.toast.title.record_deleted')", full_name + " @lang('strings.backend.profiles.deleted')");
                                    
                                    $("#profile_" + id).remove();
                                    table.row("#profile_" + id).remove().draw(false);
                                },
                                error: function(err){
                                    //console.log(err);
                                }
                            });
                        }
                    },
                    cancel: function () {
                    }
                }
            });

        });

        initialize_form2();

        $("#save-btn").on("click", function(){
            let route = "profiles";
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
                "client_engagements": profile_form_instance.form.client_engagements,
            };

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

                                console.log(data);

                                $.ajax({
                                    type: type,
                                    url: action_route,
                                    data: data,
                                    beforeSend: function(){
                                        startLoading("save-btn", "save_spinner", "save_icon");
                                    },
                                    success: function(data){
                                        endLoading("save-btn", "save_spinner", "save_icon");
                                        
                                        if(tableIsEmpty){
                                            $("#empty_filler").slideUp();
                                            $(".productsTableDiv").slideDown();
                                            tableIsEmpty = false;
                                        }

                                        
                                        let newData = table_row(data);
                                        
                                        if(action == "add"){
                                            profile_form_instance.makeToast("success", " @lang('strings.backend.toast.title.new_record_created')", data.full_name + " @lang('strings.backend.profiles.added')");
                                    
                                            table.row.add(newData).node().id = "profile_" + data.id;
                                            let rowNode = table.row("#profile_" + data.id).data(newData).draw(false).node();
                                            //console.log(rowNode);
                                            $( rowNode ).find('td').eq(2).addClass('btn-td');
                                        }
                                        else{
                                            profile_form_instance.makeToast("warning", " @lang('strings.backend.toast.title.record_updated')"," @lang('strings.backend.profiles.updated') " + data.full_name + " @lang('strings.backend.profiles.updated2')");
                                   
                                            table.row("#profile_" + data.id).data(newData).draw(false);
                                        }


                                        reset_form(true);

                                        $('#profileModal').modal('hide');
                                    },
                                    error: function(err){
                                        endLoading("save-btn", "save_spinner", "save_icon");
                                        profile_form_instance.makeToast("danger", " @lang('strings.backend.toast.title.error')", "@lang('strings.backend.general.something_went_wrong') ");   
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

                console.log(data);

                $.ajax({
                    type: type,
                    url: action_route,
                    data: data,
                    beforeSend: function(){
                        startLoading("save-btn", "save_spinner", "save_icon");
                    },
                    success: function(data){
                        endLoading("save-btn", "save_spinner", "save_icon");
                        
                        if(tableIsEmpty){
                            $("#empty_filler").slideUp();
                            $(".productsTableDiv").slideDown();
                            tableIsEmpty = false;
                        }
                        
                        let newData = table_row(data);
                        
                        if(action == "add"){
                            profile_form_instance.makeToast("success", " @lang('strings.backend.toast.title.new_record_created')", data.full_name + " @lang('strings.backend.profiles.added')");
                            table.row.add(newData).node().id = "profile_" + data.id;
                            let rowNode = table.row("#profile_" + data.id).data(newData).draw(false).node();
                            $( rowNode ).find('td').eq(2).addClass('btn-td');
                        }
                        else{
                            switch(editing){
                                case "profile":
                                    profile_form_instance.makeToast("warning"," @lang('strings.backend.toast.title.record_updated')"," @lang('strings.backend.profiles.updated') " + data.id + " @lang('strings.backend.profiles.updated2')");
                                break;
                                case "notes":
                                    profile_form_instance.makeToast("success"," @lang('strings.backend.toast.title.new_record_created')","@lang('strings.backend.profiles.added_note') " + data.full_name );
                                break;
                                case "contacts":
                                    profile_form_instance.makeToast("success"," @lang('strings.backend.toast.title.new_record_created')","@lang('strings.backend.profiles.added_contact') " + data.full_name );
                                break;
                            }

                            table.row("#profile_" + data.id).data(newData).draw(false);
                        }

                        reset_form(true);

                        $('#profileModal').modal('hide');
                    },
                    error: function(err){
                        endLoading("save-btn", "save_spinner", "save_icon");
                        profile_form_instance.makeToast("danger"," @lang('strings.backend.toast.title.error')", "@lang('strings.backend.general.something_went_wrong') ");
                    }
                });
            }
        });

        //Contact Types
        
        $("#save-btn2").on("click", function(){
                let route = "contact_types";
                let action = $(this).data("action");
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
                        endLoading("save-btn2", "save_spinner2", "save_icon2");
                        let type_name = JSON.parse(data.type_name);
                        if(locale == "en")
                            type_name = type_name.en;
                        else if (locale == "it")
                            type_name = type_name.it;
                        
                        if(action == "add"){
                            
                            profile_form_instance.makeToast("success"," @lang('strings.backend.toast.title.new_record_created')",type_name + " @lang('strings.backend.contact_types.added')");
                            
                            profile_form_instance.contact_types.push(data);
                            
                            profile_form_instance.$children.forEach(function(kid){
                                if(kid.$options.name == "contact_div"){
                                    kid.$forceUpdate();
                                }
                            });
                        }
                        
                        reset_form2();

                        $('#contactTypeModal').modal('hide');
                    },
                    error: function(err){
                        endLoading("save-btn2", "save_spinner2", "save_icon2");
                        profile_form_instance.makeToast("danger"," @lang('strings.backend.toast.title.error')", "@lang('strings.backend.general.something_went_wrong') ");
                    }
                });
            });

            $(document).on("click", ".addContactTypeBtn", function(){
                let select_id = $(this).data("select_id");
                $(select_id).select2("close");
                $('#profileModal').modal('hide');
            });

            $('#contactTypeModal').on('hidden.bs.modal', function () {
                $('#profileModal').modal('show');
            });
            
            //Add Contact / Add Profile
            $(document).on("click",".add-contact-btn", function(){
                let profile_name = $(this).data("profile_name");
                $(".profileModalLabelExtra").show();
                $("#profileModalLabelMainText").text(profile_modal_labels[3]);
                $("#profileModalLabelProfileName").text(profile_name);
                
                profile_form_instance.form_state = "Contacts";
                reset_form(false);
                

                let route = "profiles";
                let id = $(this).data("id");
                let type = "GET";
                let action_route = route;
                $("#save-btn").data("action", "edit");
                $("#save-btn").data("editing", "contacts");

                $.ajax({
                    type: type,
                    url: action_route + "/" + id,
                    beforeSend: function(){
                        startLoading("add-contacts-btn-" + id, "add-contacts-spinner-" + id, "add-contacts-icon-" + id);
                    },
                    success: function(data){
                        endLoading("add-contacts-btn-" + id, "add-contacts-spinner-" + id, "add-contacts-icon-" + id);
                        $("#new-contacts-tab").trigger("click");
                        
                        profile_form_instance.surname = data.surname;
                        profile_form_instance.firstname = data.firstname;
                        profile_form_instance.middlename = data.middlename;
                        profile_form_instance.nickname = data.nickname;
                        profile_form_instance.primary_email = data.primary_email;
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
                        
                        profile_form_instance.addContact();
                        
                        //console.log(contacts);
                        if(contacts.length > 0){
                            profile_form_instance.add_contacts = true;
                        }
                        //profile_form_instance.$forceUpdate();

                        $("#profile_id").val(data.id);
                        $("#profileModal").modal("show");
                    },
                    error: function(err){
                        endLoading("add-contacts-btn-" + id, "add-contacts-spinner-" + id, "add-contacts-icon-" + id);
                        profile_form_instance.makeToast("danger", " @lang('strings.backend.toast.title.error')","@lang('strings.backend.general.something_went_wrong') ");
                    }
                });
            });

            $(document).on("click",".add-notes-btn", function(){
                let profile_name = $(this).data("profile_name");
                $(".profileModalLabelExtra").show();
                $("#profileModalLabelMainText").text(profile_modal_labels[2]);
                $("#profileModalLabelProfileName").text(profile_name);

                profile_form_instance.form_state = "Notes";
                reset_form(false);
                

                let route = "profiles";
                let id = $(this).data("id");
                let type = "GET";
                let action_route = route;
                $("#save-btn").data("action", "edit");
                $("#save-btn").data("editing", "notes");

                $.ajax({
                    type: type,
                    url: action_route + "/" + id,
                    beforeSend: function(){
                        startLoading("add-notes-btn-" + id, "add-notes-spinner-" + id, "add-notes-icon-" + id);
                    },
                    success: function(data){
                        endLoading("add-notes-btn-" + id, "add-notes-spinner-" + id, "add-notes-icon-" + id);
                        $("#new-notes-tab").trigger("click");
                        
                        profile_form_instance.surname = data.surname;
                        profile_form_instance.firstname = data.firstname;
                        profile_form_instance.middlename = data.middlename;
                        profile_form_instance.nickname = data.nickname;
                        profile_form_instance.primary_email = data.primary_email;
                        profile_form_instance.load_editing_behavior();

                        let notes = data.notes;
                        
                        profile_form_instance.notes = notes;
                        profile_form_instance.addNote();

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

                        $("#profile_id").val(data.id);
                        $("#profileModal").modal("show");
                    },
                    error: function(err){
                        endLoading("add-notes-btn-" + id, "add-notes-spinner-" + id, "add-notes-icon-" + id);
                        profile_form_instance.makeToast("danger", " @lang('strings.backend.toast.title.error')","@lang('strings.backend.general.something_went_wrong') ");
                    }
                });
            });

            //Brands

            
            $('#brands')
            .select2({
                placeholder: '@lang('labels.backend.access.profiles.forms.select_brand')'
            }).on('select2:open', () => {
                $(".select2-results:not(:has(a))")
                .prepend('<a id="add_new_brand_link" href="#" data-toggle="modal" data-target="#newBrandModal" style="padding: 6px;height: 20px;display: inline-table;">' + add_new_brand_tooltip + '</a>');
            });
            
            $(document).on("click",".brand-btn", function(){
                let profile_name = $(this).data("profile_name");
                $("#brandModalLabelProfileName").text(profile_name);
                
                let route = "profiles";
                let id = $(this).data("id");
                let type = "GET";
                let action_route = route;
                $("#save-brands-btn").data("action", "edit");
                $("#save-brands-btn").data("editing", "brands");

                $.ajax({
                    type: type,
                    url: action_route + "/" + id,
                    beforeSend: function(){
                        startLoading("brand-btn-" + id, "brand-spinner-" + id, "brand-icon-" + id);
                    },
                    success: function(data){
                        endLoading("brand-btn-" + id, "brand-spinner-" + id, "brand-icon-" + id);
                        
                        let brands = data.brand_ids;
                        //console.log(brands);
                        $("#brands").val(brands).trigger("change");

                        $("#brands_profile_id").val(data.id);
                        $("#brandModal").modal("show");
                    },
                    error: function(err){
                        endLoading("brand-btn-" + id, "brand-spinner-" + id, "brand-icon-" + id);
                        profile_form_instance.makeToast("danger"," @lang('strings.backend.toast.title.error')", "@lang('strings.backend.general.something_went_wrong') ");
                    }
                });
            });

        //Client Engagements
        $(document).on("click",".client-engagement-btn", function(){
            let route = "profiles";
            let id = $(this).data("id");
            let name = $(this).data("profile_name");
            
            let type = "GET";
            let action_route = route;

            $.ajax({
                type: type,
                url: action_route + "/" + id,
                success: function(data){
                    profiles_page_instance.modalAddClientEngagement();
                    profiles_page_instance.form.profile_name = name;
                    profiles_page_instance.form.profile_id = id;
                    profiles_page_instance.form.action = "Edit";
                    profiles_page_instance.form.client_engagements = data.client_engagements;
                    profiles_page_instance.addClientEngagement();
                },
                error: function(err){
                    profile_form_instance.makeToast("danger", " @lang('strings.backend.toast.title.error')", "@lang('strings.backend.general.something_went_wrong') ");
                }
            });
        });
            
        //Contact Types
        
        $("#save-brands-btn").on("click", function(){
                let route = "link_to_brand";
                let action = $(this).data("action");
                let type = "POST";
                let action_route = route + "/" + $("#brands_profile_id").val();


                let data = {
                    "brands": $("#brands").val()
                };

                
                $.ajax({
                    type: type,
                    url: action_route,
                    data: data,
                    beforeSend: function(){
                        startLoading("save-brands-btn", "save_brands_spinner", "save_brands_icon");
                    },
                    success: function(data){
                        endLoading("save-brands-btn", "save_brands_spinner", "save_brands_icon");
                        let brands = data.brand_ids;
                        let newData = table_row(data);
                                        
                        profile_form_instance.makeToast("warning"," @lang('strings.backend.toast.title.record_updated')", "@lang('strings.backend.profiles.brands_updated') " + data.full_name + " @lang('strings.backend.profiles.brands_updated2')");
                        
                        if(loaded_brand == ""){
                            if(data.brand_names != ""){
                                $("#profile_" + data.id).remove();
                                table.row("#profile_" + data.id).remove().draw(false);
                            }
                            else{
                                table.row("#profile_" + data.id).data(newData).draw(false);
                            }
                        }
                        else{
                            //console.log("Brand IDS", data.brand_ids);
                            //console.log("Loaded Brand",loaded_brand);
                            if(data.brand_ids.includes(parseInt(loaded_brand))){
                                table.row("#profile_" + data.id).data(newData).draw(false);
                            }
                            else{
                                $("#profile_" + data.id).remove();
                                table.row("#profile_" + data.id).remove().draw(false);
                            }
                        }

                        $('#brandModal').modal('hide');
                    },
                    error: function(err){
                        endLoading("save-brands-btn", "save_brands_spinner", "save_brands_icon");
                        //console.log(err);
                        profile_form_instance.makeToast("danger"," @lang('strings.backend.toast.title.error')", "@lang('strings.backend.general.something_went_wrong') ");
                            
                    }
                });
            });
            
            $(document).on("click", "#add_new_brand_link", function(){
                $('#brandModal').modal('hide');
                $("#brands").select2("close");
            });
            
            $('#newBrandModal').on('hidden.bs.modal', function () {
                $('#brandModal').modal('show');
            });

            
            $("#save-new-brand-btn").on("click", function(){
                let route = "brands";
                let type = "POST";
                let action_route = route;

                let data = {
                    "name": $("#name").val(),
                    "website": $("#website").val(),
                    "logo": $("#logo").val(),
                };

                $.ajax({
                    type: type,
                    url: action_route,
                    data: data,
                    beforeSend: function(){
                        startLoading("save-new-brand-btn", "save_new_brand_spinner", "save_new_brand_icon");
                    },
                    success: function(data){
                        endLoading("save-new-brand-btn", "save_new_brand_spinner", "save_new_brand_icon");
                        // Create a DOM Option and pre-select by default
                        var newOption = new Option(data.name, data.id, true, true);
                        // Append it to the select
                        $('#brands').append(newOption).trigger('change');

                        //Reload sidebar links
                        $.ajax({
                            type: "GET",
                            url: "brands/all",
                            success: function(data){
                                //console.log(data);
                                let active_none = (loaded_brand == "") ? " active" : "";
                                let profile_links = '\
                                    <li class="nav-item">\
                                        <a href="profiles?brand=none" class="nav-link ' + active_none + '">\
                                            ' + no_brands_text + '\
                                        </a>\
                                    </li>';

                                data.forEach(function(brand_data){
                                    let is_active = (loaded_brand == brand_data.id) ? " active" : "";

                                    profile_links +=  '\
                                    <li id="profiles-links-brand-' + brand_data.id + '" class="nav-item' + is_active + '">\
                                        <a href="profiles?brand=' + brand_data.name.toLowerCase() + '" class="nav-link ">\
                                            ' + brand_data.name + '\
                                        </a>\
                                    </li>';
                                });
                                
                                $("#profiles-links").html(profile_links);
                            },
                        });

                        reset_new_brand_form();

                        $('#newBrandModal').modal('hide');
                        $('#brandModal').modal('show');
                    },
                    error: function(err){
                        endLoading("save-new-brand-btn", "save_new_brand_spinner", "save_new_brand_icon");
                        //console.log(err);
                        profile_form_instance.makeToast("danger"," @lang('strings.backend.toast.title.error')", "@lang('exceptions.backend.general.unknown_error')");
                    }
                });
            });
            
            $(document).on("click", "#addProvinceBtn", function(){
                $('#profileModal').modal('hide');
                let country_id = $(this).data("country_id");
                $('#save-new-province-btn').data('country_id', country_id);
            });

            $('#addProvinceModal').on('hidden.bs.modal', function () {
                $('#profileModal').modal('show');
            });

            $("#save-new-province-btn").on("click", function(){
                let route = "/api/provinces/";
                let type = "POST";
                let action_route = route;
                let country_id = $(this).data("country_id");

                let data = {
                    "name": $("#addProvinceModal #province_name").val()
                };

                $.ajax({
                    type: type,
                    url: action_route + country_id,
                    data: data,
                    beforeSend: function(){
                        startLoading("save-new-province-btn", "save_new_province_spinner", "save_new_province_icon");
                    },
                    success: function(data){
                        endLoading("save-new-province-btn", "save_new_province_spinner", "save_new_province_icon");
                        profile_form_instance.loadProvinces();
                        profile_form_instance.makeToast("success", " @lang('strings.backend.toast.title.new_record_created')", data.name + "@lang('labels.backend.access.locations.provinces.has_been_created')");
                        reset_new_province_form();

                        $('#addProvinceModal').modal('hide').on('hidden', function(){
                            $('#profileModal').modal('show');
                        });
                    },
                    error: function(err){
                        endLoading("save-new-province-btn", "save_new_province_spinner", "save_new_province_icon");
                        //console.log(err);
                        profile_form_instance.makeToast("danger", " @lang('strings.backend.toast.title.error')","@lang('exceptions.backend.general.unknown_error')");
                    }
                });
            });

            $(document).on("click", "#addCityBtn", function(){
                $('#profileModal').modal('hide');

                let province_id = $(this).data("province_id");
                //console.log(province_id);
                $('#save-new-city-btn').data('province_id', province_id);
            });

            $('#addCityModal').on('hidden.bs.modal', function () {
                $('#profileModal').modal('show');
            });

            $("#save-new-city-btn").on("click", function(){
                let route = "/api/cities/";
                let type = "POST";
                let action_route = route;
                let province_id = $(this).data("province_id");

                let data = {
                    "name": $("#addCityModal #city_name").val()
                };

                $.ajax({
                    type: type,
                    url: action_route + province_id,
                    data: data,
                    beforeSend: function(){
                        startLoading("save-new-city-btn", "save_new_city_spinner", "save_new_city_icon");
                    },
                    success: function(data){
                        endLoading("save-new-city-btn", "save_new_city_spinner", "save_new_city_icon");
                        profile_form_instance.loadCities();
                        profile_form_instance.makeToast("success"," @lang('strings.backend.toast.title.new_record_created')", data.name + "@lang('labels.backend.access.locations.cities.has_been_created')");
                        reset_new_city_form();
                        
                        $('#addCityModal').modal('hide').on('hidden', function(){
                            $('#profileModal').modal('show');
                        });
                    },
                    error: function(err){
                        endLoading("save-new-city-btn", "save_new_city_spinner", "save_new_city_icon");
                        //console.log(err);
                        profile_form_instance.makeToast("danger"," @lang('strings.backend.toast.title.error')", "@lang('exceptions.backend.general.unknown_error')");
                    }
                });
            });
    });
</script>
@endpush