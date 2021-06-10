@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.workforce_management.color_coding'))


@section('content')
<workforce-management-color-coding-index :brand_session="{{ session()->has('brand') ? \Session::get('brand') : 'false'}}"></workforce-management-color-coding-index>
@endsection
