@extends('backend.layouts.app')

@section('title', app_name().' | ' . __('menus.backend.payment.plan'))


@section('content')

<payment-plan></payment-plan>
 
@endsection