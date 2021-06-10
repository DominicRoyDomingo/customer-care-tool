@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.profiles.management'))

@section('content')

    <profiles-show :profile_id="{{ $profile_id }}"  :loaded_brand="{{ request()->brand }}"></profiles-show>
@endsection
