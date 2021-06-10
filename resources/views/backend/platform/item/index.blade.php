@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.platform'))

@section('content')
<div class="row">
    <div class="col">
        <platform-item-component></platform-item-component>
    </div><!--col-->
</div><!--row-->

@endsection
