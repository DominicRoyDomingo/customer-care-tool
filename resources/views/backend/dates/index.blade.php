@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.dates'))

@section('content')
<div class="row">
    <div class="col">
        <dates-component
        :header="`@lang('labels.backend.access.platform.table.list')`"></dates-component>
    </div><!--col-->
</div><!--row-->
@endsection