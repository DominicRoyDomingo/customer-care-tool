@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.algolia_class.management'))

@section('content')
    <algolia-classes></algolia-classes>
@endsection
