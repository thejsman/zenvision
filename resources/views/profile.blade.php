@extends('layouts.app')

@section('title')
    {{ config('app.name', 'Skote') }}
@endsection
@section('content')
    <dynamic-component component="{{ $component }}" />
@endsection
