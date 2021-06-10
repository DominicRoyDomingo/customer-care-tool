@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.provider_services.management'))

@section('content')
    <provider-services></provider-services>
@endsection
