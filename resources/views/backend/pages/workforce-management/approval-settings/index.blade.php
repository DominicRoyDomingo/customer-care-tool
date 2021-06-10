@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.workforce_management.approval_settings'))


@section('content')
<workforce-management-approval-settings-index :brand_session="{{ session()->has('brand') ? \Session::get('brand') : 'false'}}"></workforce-management-approval-settings-index>
@endsection
