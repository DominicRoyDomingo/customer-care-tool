@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.workforce_management.reasons'))


@section('content')
<workforce-management-reasons-index :brand_session="{{ session()->has('brand') ? \Session::get('brand') : 'false'}}"></workforce-management-reasons-index>
@endsection
