@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.profiles.management'))

@section('content')
    <organization-categories></organization-categories>
@endsection
