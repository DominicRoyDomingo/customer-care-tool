@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.content'))

@section('content')
<div class="row">
    {{-- {{ dd($items) }} --}}
    <div class="col">
    <content-history></content-history>
    </div><!--col-->
</div><!--row-->
@endsection
