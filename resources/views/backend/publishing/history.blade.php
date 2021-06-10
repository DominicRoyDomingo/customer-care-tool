@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.publishing.publishing'))

@section('content')
<div class="row">
    {{-- {{ dd($items) }} --}}
    <div class="col">
    <publishing-history></publishing-history>
    </div><!--col-->
</div><!--row-->
@endsection
