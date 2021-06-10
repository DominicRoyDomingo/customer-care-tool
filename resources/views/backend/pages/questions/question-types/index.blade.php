@extends('backend.layouts.app')

@section('title', app_name() . ' | '. __('labels.backend.access.questions.question_types.question_types'))


@section('content')
<questions-question-types></questions-question-types>
@endsection
