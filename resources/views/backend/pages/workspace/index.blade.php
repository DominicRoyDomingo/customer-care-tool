@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.workspace.management'))

@section('content')
    <workspaces-component :active_organization="{{session()->get('active_organization')}}"></workspaces-component>
@endsection