@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.provider_groups.management'))

@section('content')
    <provider-groups></provider-groups>
@endsection
