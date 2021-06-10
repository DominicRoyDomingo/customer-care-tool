@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.algolia_permission.management'))

@section('content')
    <algolia-permissions></algolia-permissions>
@endsection
