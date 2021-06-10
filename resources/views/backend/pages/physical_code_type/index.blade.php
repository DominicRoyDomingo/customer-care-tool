@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.physical_code_type.management'))

@section('content')
    <physical-code-type></physical-code-type>
@endsection
