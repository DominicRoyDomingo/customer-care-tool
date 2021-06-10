@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.information_type.management'))

@section('content')
    <information-type></information-type>
@endsection
