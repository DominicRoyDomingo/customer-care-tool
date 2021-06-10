@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.roles.items'))

@section('content')
<div class="row">
    {{-- {{ dd($items) }} --}}
    <div class="col">
    <item-history></item-history>
    </div><!--col-->
</div><!--row-->
@endsection
