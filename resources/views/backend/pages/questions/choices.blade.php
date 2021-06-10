@extends('backend.layouts.app')

@section('title', app_name().' | ' . __('menus.backend.choices.item'))


@section('content')

<questions-choice></questions-choice>
 
@endsection