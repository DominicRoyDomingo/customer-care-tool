@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.campaign'))


@section('content')
<template-index></template-index>
@endsection
