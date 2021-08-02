@extends('layouts.app')

@section('title')
{{ config('app.name', 'Shopify Register') }}
@endsection
@section('content')
<dynamic-component component="{{ $component }}" />
@endsection