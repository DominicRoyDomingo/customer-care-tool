@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.tags'))

@section('content')
<div class="row">
    <div class="col">
        <tags-component
        :header="`@lang('labels.backend.access.tags.table.list')`"></tags-component>
    </div><!--col-->
</div><!--row-->
@endsection
