@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.profiles.management'))

@section('content')
    <profiles-index loaded_brand="{{ request()->brand }}"></profiles-index>
@endsection
