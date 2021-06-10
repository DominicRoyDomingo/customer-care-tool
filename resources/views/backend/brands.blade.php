@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.brands.management'))

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
    <h5 id="success_added_message" style="display:none;">@lang('strings.backend.brands.added')</h5>
    <h5 id="success_updated_message" style="display:none;">@lang('strings.backend.brands.updated')</h5>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-5">
                <h4 class="card-title mb-0">
                    {{ __('labels.backend.access.brands.management') }} <small class="text-muted">{{ __('labels.backend.access.brands.active') }}</small>
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
                            <th>@lang('labels.backend.access.brands.table.logo')</th>
                            <th>@lang('labels.backend.access.brands.table.brand_name')</th>
                            <th>@lang('labels.backend.access.brands.table.website')</th>
                            <th>@lang('labels.general.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($brands as $brand)
                            <tr id="brand_{{ $brand->id }}">
                                <td>{{ $brand->logo_display }}</td>
                                <td>{{ $brand->name }}</td>
                                <td>{{ $brand->website }}</td>
                                <td class="btn-td">
                                    <button data-id="{{ $brand->id }}" id="edit-btn-{{ $brand->id }}" data-toggle="tooltip" data-placement="top" title="@lang('buttons.general.crud.edit')" class="btn btn-warning edit-btn">
                                        <i class="fas fa-edit" id="edit-icon-{{ $brand->id }}"></i>
                                        <i class="fas fa-spinner fa-spin" id="edit-spinner-{{ $brand->id }}" style="display:none;"></i>
                                    </button>

                                    <button data-id="{{ $brand->id }}" data-name="{{ $brand->name }}" id="delete-btn-{{ $brand->id }}"  data-toggle="tooltip" data-placement="top" title="@lang('buttons.general.crud.delete')" class="btn btn-danger delete-btn">
                                        <i class="fas fa-trash" id="delete-icon-{{ $brand->id }}" ></i>
                                        <i class="fas fa-spinner fa-spin" id="delete-spinner-{{ $brand->id }}" style="display:none;"></i>
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
<div class="modal fade" id="brandModal" tabindex="-1" role="dialog" aria-labelledby="brandModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="brandModalLabel">@lang('labels.backend.access.brands.forms.title')</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
              <div class="col-md-12">
                <div class="row">
                      <div class="col-md-3">
                            <label for="name" class="mt-2">@lang('labels.backend.access.brands.forms.brand_name')</label>
                      </div>
                      <div class="col-md-9">
                            <input type="text" name="name" id="name" class="form-control">
                      </div>
                </div>
                <div class="row mt-1">
                      <div class="col-md-3">
                            <label for="website" class="mt-2">@lang('labels.backend.access.brands.forms.website')</label>
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
            <input type="hidden" name="brand_id" id="brand_id" class="form-control">
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
        let tableIsEmpty = {{ (count($brands) == 0) ? 1 : 0}};
        let locale = '{{strtolower(app()->getLocale())}}';
        let no_brands_text = '@lang('menus.backend.sidebar.no_brand')';
        function table_row(data){
            var output = {
                    "0": data.logo_display,
                    "1": data.name,
                    "2": data.website,
                    "3": '\
                        <button data-toggle="tooltip" id="edit-btn-' + data.id + '" data-placement="top" title="' + edit_tooltip + '" data-id="' + data.id + '" class="btn btn-warning edit-btn">\
                            <i class="fas fa-edit" id="edit-icon-' + data.id + '"></i>\
                            <i class="fas fa-spinner fa-spin" id="edit-spinner-' + data.id + '" style="display:none;"></i>\
                        </button>\
                        <button  data-toggle="tooltip" id="delete-btn-' + data.id + '" data-name="' + data.name + '"  data-placement="top" title="' + delete_tooltip + '" data-id="' + data.id + '" class="btn btn-danger delete-btn">\
                            <i class="fas fa-trash" id="delete-icon-' + data.id + '"></i>\
                            <i class="fas fa-spinner fa-spin" id="delete-spinner-' + data.id + '" style="display:none;"></i>\
                        </button>\
                    ',
                };

            return output;
        }

        function reset_form(){
            $("#name").val("");
            $("#website").val("");
            $("#logo").val("");
            $("#brand_id").val("");
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
                            "width": '25%',
                            "className": 'text-left',
                        },
                        {
                            "targets": 1,
                            "width": '25%',
                            "className": 'text-center',
                        },
                        {
                            "targets": 2,
                            "width": '30%',
                            "className": 'text-center',
                        },
                        {
                            "targets": 3,
                            "width": '20%',
                            "className": 'text-center',
                        }
                    ]
                });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#add-btn").on("click", function(){
                $("#brandModal").modal("show");
                $("#save-btn").data("action", "add");
            });

            $(document).on("click", ".edit-btn", function(){
                $("#modal-error").hide();
                let route = "brands";
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

                        $("#name").val(data.name);
                        $("#website").val(data.website);
                        $("#logo").val(data.logo);
                        $("#brand_id").val(data.id);
                        $("#brandModal").modal("show");
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
                let route = "brands";
                let id = $(this).data("id");
                let name = $(this).data("name");
                let type = "DELETE";
                let action_route = route;
                $.confirm({
                    title: 'Confirm Action',
                    content: 'You are about to delete the brand \'' + name + "'",
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
                                        show_alert(name + " @lang('strings.backend.brands.deleted')", "danger", "fas fa fa-trash");
                                        $("#brand_" + id).remove();
                                        $("#profiles-links-brand-" + id).remove();
                                        table.row("#brand_" + id).remove().draw(false);
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
                let route = "brands";
                let action = $(this).data("action");
                let type = "POST";
                let action_route = route;

                if(action != "add"){
                    type = "PUT";
                    action_route += "/" + $("#brand_id").val();
                }

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
                        startLoading("save-btn", "save_spinner", "save_icon");
                    },
                    success: function(data){
                        $("#modal-error").hide();
                        endLoading("save-btn", "save_spinner", "save_icon");
                                       
                        if(tableIsEmpty){
                            $("#empty_filler").slideUp();
                            $(".productsTableDiv").slideDown();
                            tableIsEmpty = false;
                        }
                        
                        
                        let newData = table_row(data);
                        
                        if(action == "add"){
                            show_alert(data.name + " @lang('strings.backend.brands.added')", "success", "fas fa fa-info-circle");
                            table.row.add(newData).node().id = "brand_" + data.id;
                            table.row("#brand_" + data.id).data(newData).draw(false);
                        }
                        else{
                            show_alert(" @lang('strings.backend.brands.updated') " + data.name + " @lang('strings.backend.brands.updated2')", "warning", "fas fa fa-info-circle");
                            table.row("#brand_" + data.id).data(newData).draw(false);
                        }
                        
                        //Reload sidebar links
                        $.ajax({
                            type: "GET",
                            url: "brands/all",
                            success: function(data){
                                console.log(data);
                                let profile_links = '\
                                    <li class="nav-item">\
                                        <a href="profiles?brand=none" class="nav-link ">\
                                            ' + no_brands_text + '\
                                        </a>\
                                    </li>';
                                data.forEach(function(brand_data){
                                    profile_links +=  '\
                                    <li id="profiles-links-brand-' + brand_data.id + '" class="nav-item">\
                                        <a href="profiles?brand=' + brand_data.name.toLowerCase() + '" class="nav-link ">\
                                            ' + brand_data.name + '\
                                        </a>\
                                    </li>';
                                });

                                $("#profiles-links").html(profile_links);
                            },
                        });

                        reset_form();

                        $('#brandModal').modal('hide');
                    },
                    error: function(err){
                        endLoading("save-btn", "save_spinner", "save_icon");
                        console.log(err);
                        show_alert("@lang('exceptions.backend.general.unknown_error')", "danger", "fas fa fa-info");
                    }
                });
            });
        });
    </script>
@endpush