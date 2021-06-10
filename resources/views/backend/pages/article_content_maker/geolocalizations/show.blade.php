@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.geolocalizations.management'))

@section('content')
{{-- {{dd($article)}} --}}
    <geolocalizations-show-component :article="{{$article}}"></geolocalizations-show-component>
@endsection