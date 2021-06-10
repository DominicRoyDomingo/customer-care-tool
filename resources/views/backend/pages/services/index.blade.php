@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.services.management'))

@section('content')
    <services-component></services-component>
@endsection
