@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('menus.backend.sidebar.html_tags_priority') )


@section('content')

<html-tags-priority-list></html-tags-priority-list>
 
@endsection
