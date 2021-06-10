@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('menus.backend.sidebar.terminilogies')  )

@section('content')
    <terminologies-list></terminologies-list>
@endsection
