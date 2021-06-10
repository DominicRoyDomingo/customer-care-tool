@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.content'))

@section('content')
<div class="row">
    <div class="col">
    <content-item :contents="{{ json_encode($contents[0]) }}"></content-item>
    </div><!--col-->
</div><!--row-->
@endsection
