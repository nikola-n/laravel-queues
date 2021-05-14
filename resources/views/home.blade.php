@extends('layouts.app')

@section('content')
<div class="container">
    <task-list :data-project="{{ $project }}"></task-list>
</div>
@endsection
