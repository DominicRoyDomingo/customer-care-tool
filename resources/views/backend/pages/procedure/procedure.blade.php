@extends('backend.layouts.app')

@section('title', 'CustomerCareTool | Procedure List')


@section('content')

<procedure-list :brand_session="{{ session()->has('brand') ? \Session::get('brand') : 'false'}}"></procedure-list>
 
@endsection