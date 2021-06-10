@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('menus.backend.sidebar.descriptions') )

@section('content')
    <term-description></term-description>
@endsection
