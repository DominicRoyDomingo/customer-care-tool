@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.contact_types.management'))

@push("after-styles")
<style>
    .local_inputs {
        display: none;
    }
    </style>
@endpush
@section('content')

<div class="alert alert-success alert-dismissible" id="alert"
style="display:none; position:absolute; z-index: 100000000000; right: 5vh; top: 8vh;" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h5 id="success_added_message" style="display:none;">@lang('strings.backend.contact_types.added')</h5>
    <h5 id="success_updated_message" style="display:none;">@lang('strings.backend.contact_types.updated')</h5>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-5">
                <h4 class="card-title mb-0">
                    {{ __('labels.backend.access.contact_types.management') }} <small class="text-muted">{{ __('labels.backend.access.contact_types.active') }}</small>
                </h4>
            </div><!--col-->

            <div class="col-sm-7">
                <button id="add-btn" class="btn btn-success ml-1 float-right" data-toggle="tooltip" title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></button>
            </div><!--col-->
        </div><!--row-->

        <div class="row mt-4">
            <div class="col table-responsive">
                    <table class="table table-striped" id="table"  style="width: 100% !important;">
                        <thead>
                        <tr>
                            <th>@lang('labels.backend.access.contact_types.table.type_name')</th>
                            <th>@lang('labels.general.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($contact_types as $contact_type)
                            <tr id="contact_type_{{ $contact_type->id }}">
                                <td>{{ $contact_type->localized_name }}</td>
                                <td class="btn-td">
                                    <button data-id="{{ $contact_type->id }}" id="edit-btn-{{ $contact_type->id }}" data-toggle="tooltip" data-placement="top" title="@lang('buttons.general.crud.edit')" class="btn btn-warning edit-btn">
                                        <i class="fas fa-edit" id="edit-icon-{{ $contact_type->id }}"></i>
                                        <i class="fas fa-spinner fa-spin" id="edit-spinner-{{ $contact_type->id }}" style="display:none;"></i>
                                    </button>

                                    <button data-id="{{ $contact_type->id }}" data-localized_name="{{ $contact_type->localized_name }}" id="delete-btn-{{ $contact_type->id }}"  data-toggle="tooltip" data-placement="top" title="@lang('buttons.general.crud.delete')" class="btn btn-danger delete-btn">
                                        <i class="fas fa-trash" id="delete-icon-{{ $contact_type->id }}" ></i>
                                        <i class="fas fa-spinner fa-spin" id="delete-spinner-{{ $contact_type->id }}" style="display:none;"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
            </div><!--col-->
        </div><!--row-->
    </div><!--card-body-->
</div><!--card-->

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
                    <div class="alert alert-danger" id="modal-error" role="alert" style="display:none"></div>
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
            <button id="save-btn" data-action="add" type="button" class="btn btn-primary">
                <i class="fas fa-save" id="save_icon"></i>
                <i class="fas fa-spinner fa-spin" id="save_spinner" style="display:none;"></i>
                Save changes
            </button>
        </div>
      </div>
    </div>
</div>
@endsection


@push("after-scripts")
    <script>
        let table = null;
        let edit_tooltip = '@lang('buttons.tooltip.edit')';
        let delete_tooltip = '@lang('buttons.tooltip.delete')';
        let tableIsEmpty = {{ (count($contact_types) == 0) ? 1 : 0}};
        let locale = '{{strtolower(app()->getLocale())}}';

        function table_row(data){
            var output = {
                    "0": data.localized_name,
                    "1": '\
                        <button data-toggle="tooltip" id="edit-btn-' + data.id + '" data-placement="top" title="' + edit_tooltip + '" data-id="' + data.id + '" class="btn btn-warning edit-btn">\
                            <i class="fas fa-edit" id="edit-icon-' + data.id + '"></i>\
                            <i class="fas fa-spinner fa-spin" id="edit-spinner-' + data.id + '" style="display:none;"></i>\
                        </button>\
                        <button  data-toggle="tooltip" id="delete-btn-' + data.id + '" data-localized_name="' + data.localized_name + '"  data-placement="top" title="' + delete_tooltip + '" data-id="' + data.id + '" class="btn btn-danger delete-btn">\
                            <i class="fas fa-trash" id="delete-icon-' + data.id + '"></i>\
                            <i class="fas fa-spinner fa-spin" id="delete-spinner-' + data.id + '" style="display:none;"></i>\
                        </button>\
                    ',
                };

            return output;
        }

        function reset_form(){
            $(".local_inputs").val("");
        }

        function initialize_form(){
            $("." + locale + "_inputs").show();
            $("#language").val(locale).trigger("change");
        }
        
        $(function(){
            table = $('#table').DataTable({
                    "columnDefs": [
                        {
                            "targets": 0,
                            "width": '80%',
                            "className": 'text-left',
                        },
                        {
                            "targets": 1,
                            "width": '20%',
                            "className": 'text-center',
                        },
                    ]
                });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#add-btn").on("click", function(){
                $("#contactTypeModal").modal("show");
                $("#save-btn").data("action", "add");
            });

            $(document).on("click", ".edit-btn", function(){
                $("#modal-error").hide();
                let route = "contact_types";
                let id = $(this).data("id");
                let type = "GET";
                let action_route = route;
                $("#save-btn").data("action", "edit");

                $.ajax({
                    type: type,
                    url: action_route + "/" + id,
                    beforeSend: function(){
                        startLoading("edit-btn-" + id, "edit-spinner-" + id, "edit-icon-" + id);
                    },
                    success: function(data){
                        $("#modal-error").hide();
                        endLoading("edit-btn-" + id, "edit-spinner-" + id, "edit-icon-" + id);
                        let type_name = JSON.parse(data.type_name);
                        $("#type_name_en").val(type_name.en);
                        $("#type_name_it").val(type_name.it);
                        $("#contact_type_id").val(data.id);
                        $("#contactTypeModal").modal("show");
                    },
                    error: function(err){
                        endLoading("edit-btn-" + id, "edit-spinner-" + id, "edit-icon-" + id);
                        console.log(err);
                        $("#modal-error").html("Something went wrong").slideDown();
                        setTimeout(function(){
                            $("#modal-error").fadeOut();
                        }, 3000);
                    }
                });
            });

            $(document).on("click", ".delete-btn", function(){
                let route = "contact_types";
                let id = $(this).data("id");
                let localized_name = $(this).data("localized_name");
                let type = "DELETE";
                let action_route = route;
                $.confirm({
                    title: 'Confirm Action',
                    content: 'You are about to delete the contact type \'' + localized_name + "'",
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
                                        $("#contact_type_" + id).remove();
                                        table.row("#contact_type_" + id).remove().draw(false);
                                    },
                                    error: function(err){
                                        endLoading("delete-btn-" + id, "delete-spinner-" + id, "delete-icon-" + id);
                                        console.log(err);
                                    }
                                });
                            }
                        },
                        cancel: function () {
                        }
                    }
                });

            });

            initialize_form();

            $("#language").on("change", function(){
                let language = $(this).val();

                $(".local_inputs").hide();
                $("." + language + "_inputs").show();
            });

            $("#save-btn").on("click", function(){
                $("#modal-error").hide();
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
                        startLoading("save-btn", "save_spinner", "save_icon");
                    },
                    success: function(data){
                        $("#modal-error").hide();
                        endLoading("save-btn", "save_spinner", "save_icon");
                        let type_name = JSON.parse(data.type_name);
                        if(locale == "en")
                            type_name = type_name.en;
                        else if (locale == "it")
                            type_name = type_name.it;

                        if(tableIsEmpty){
                            $("#empty_filler").slideUp();
                            $(".productsTableDiv").slideDown();
                            tableIsEmpty = false;
                        }
                        
                        setTimeout(function(){
                            $("#alert").fadeOut();
                        }, 3000);

                        
                        let newData = table_row(data);
                        
                        if(action == "add"){
                            $("#added_message").show();
                            $("#alert").fadeIn();
                            table.row.add(newData).node().id = "contact_type_" + data.id;
                            table.row("#contact_type_" + data.id).data(newData).draw(false);
                        }
                        else{
                            $("#updated_message").show();
                            $("#alert").fadeIn();
                            table.row("#contact_type_" + data.id).data(newData).draw(false);
                        }
                        
                        reset_form();

                        $('#contactTypeModal').modal('hide');
                    },
                    error: function(err){
                        endLoading("save-btn", "save_spinner", "save_icon");
                        console.log(err);
                        $("#modal-error").html("Something went wrong").slideDown();
                        setTimeout(function(){
                            $("#modal-error").fadeOut();
                        }, 3000);
                    }
                });
            });
        });
    </script>
@endpush