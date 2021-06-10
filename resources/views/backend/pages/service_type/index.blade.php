@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.service_type.management'))

@section('content')
    <service-type></service-type>
@endsection
