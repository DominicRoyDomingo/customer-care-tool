@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.services_exclusive.management'))

@section('content')
    <services-exclusive-component></services-exclusive-component>
@endsection
