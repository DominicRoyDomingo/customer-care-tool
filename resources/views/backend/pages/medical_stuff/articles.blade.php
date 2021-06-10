@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('menus.backend.publishing.item') )


@section('content')

<publishing-items></publishing-items>
 
@endsection
