@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.publishing.type'))

@section('content')
<div class="row">
    <div class="col">
        <item-type-component></item-type-component>
    </div><!--col-->
</div><!--row-->
@endsection
