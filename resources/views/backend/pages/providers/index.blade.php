@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.providers.management'))

@section('content')
    <providers-component></providers-component>
@endsection
