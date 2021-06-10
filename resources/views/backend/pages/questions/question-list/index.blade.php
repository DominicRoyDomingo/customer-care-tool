@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.questions.question_list.question_list'))


@section('content')
<questions-question-list></questions-question-list>
@endsection
