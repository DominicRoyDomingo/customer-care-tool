@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.workforce_management.calendar_notes'))


@section('content')
<workforce-management-calendar-notes-index :brand_session="{{ session()->has('brand') ? \Session::get('brand') : 'false'}}"></workforce-management-calendar-notes-index>
@endsection
