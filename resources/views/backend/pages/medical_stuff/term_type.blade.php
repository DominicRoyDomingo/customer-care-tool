@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('menus.backend.sidebar.type_of_terms')  )


@section('content')
<term-type></term-type>
@endsection
