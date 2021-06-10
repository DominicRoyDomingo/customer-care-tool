@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.profiles.management'))

@section('content')
    <locations-index></locations-index>
@endsection
