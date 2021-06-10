@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.actors.management'))

@section('content')
    <actors-component></actors-component>
@endsection
