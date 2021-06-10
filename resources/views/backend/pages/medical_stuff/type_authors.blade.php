@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('menus.backend.sidebar.type_of_author') )


@section('content')

<author-type-list></author-type-list>
 
@endsection
