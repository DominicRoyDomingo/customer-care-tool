@extends('backend.layouts.app')

@section('title', app_name(). " | {$pageTitle}")

@section('content')
<questionnaire-index></questionnaire-index>
@endsection
