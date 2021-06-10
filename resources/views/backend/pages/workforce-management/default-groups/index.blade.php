@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.workforce_management.default_groups'))


@section('content')
<workforce-management-default-groups-index :brand_session="{{ session()->has('brand') ? \Session::get('brand') : 'false'}}"></workforce-management-default-groups-index>
@endsection
