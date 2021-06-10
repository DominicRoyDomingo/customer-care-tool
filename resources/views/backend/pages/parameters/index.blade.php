@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.parameters.management'))

@section('content')
    <parameters-component></parameters-component>
@endsection
