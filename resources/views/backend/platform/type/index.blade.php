@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.platform'))

@section('content')
<div class="row">
    <div class="col">
        <platform-type-component></platform-type-component>
    </div><!--col-->
</div><!--row-->
@endsection
