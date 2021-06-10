@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.items'))

@section('content')
<div class="row">
    <div class="col">
        <content-items-component content_id="{{$contentId}}" content_name="{{$contentName}}"></content-items-component>
    </div><!--col-->
</div><!--row-->
@endsection