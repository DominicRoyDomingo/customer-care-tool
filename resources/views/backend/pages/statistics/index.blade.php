@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.statistics.management'))

@section('content')
    <statistics-index></statistics-index>
@endsection