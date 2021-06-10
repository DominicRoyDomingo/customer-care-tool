@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.publishing.publishing'))

@section('content')
<div class="row">
    <div class="col">
        <publishing-component></publishing-component>
    </div><!--col-->
</div><!--row-->
@endsection