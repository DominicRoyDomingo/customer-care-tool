@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.workforce_management.request_type'))


@section('content')
<workforce-management-request-type-index :brand_session="{{ session()->has('brand') ? \Session::get('brand') : 'false'}}"></workforce-management-request-type-index>
@endsection
