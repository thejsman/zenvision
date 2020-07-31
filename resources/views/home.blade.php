
@extends('layouts.app')
@php
    $component = (isset($group) ? "{$group}-" : "").$component;
    $title = __("page-titles.{$component}" );
    if(strpos($title, 'page-titles.') !== false) {
        $title = "404 Page Not Found";
    }
@endphp
@section('title')
   {{ $title }} | {{ config('app.name', 'Skote') }}
@endsection
@section('content')
<dynamic-component component="{{ $component }}" />
@endsection
