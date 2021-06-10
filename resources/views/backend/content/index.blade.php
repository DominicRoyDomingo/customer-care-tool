@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.content'))

@section('content')
<div class="row">
    <div class="col">
        <content-component></content-component>
    </div><!--col-->
</div><!--row-->
@endsection

