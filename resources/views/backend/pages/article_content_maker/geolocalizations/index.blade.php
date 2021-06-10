@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.geolocalizations.management'))

@section('content')
    <geolocalizations-component></geolocalizations-component>
@endsection
