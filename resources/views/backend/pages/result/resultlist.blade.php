@extends('backend.layouts.app')

@section('title', 'CustomerCareTool | Result List')


@section('content')

<result-list :brand_session="{{ session()->has('brand') ? \Session::get('brand') : 'false'}}"></result-list>
 
@endsection