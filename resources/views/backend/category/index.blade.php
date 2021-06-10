@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.category'))

@section('content')
<div class="row">
    <div class="col">
        <category-component></category-component>
    </div><!--col-->
</div><!--row-->
@endsection
