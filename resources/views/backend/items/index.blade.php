@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.items'))

@section('content')
<div class="row">
    <div class="col">
        <items-component></items-component>
    </div><!--col-->
</div><!--row-->
@endsection
