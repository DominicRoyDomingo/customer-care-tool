@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('menus.backend.sidebar.terminilogies') .' | '. $medicalTerm->term_name )


@section('content')
    <term-show :id="{{ $medicalTerm->id }}"></term-show>
@endsection
