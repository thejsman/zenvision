@extends('layouts.app')
@section('title')
   {{ __('page-titles.reset') }} | {{ config('app.name', 'Skote') }}
@endsection
@section('content')
<div>
   
    <div class="account-pages my-5 pt-5">
        <div class="container">
        <forgot-password
            submit-url="{{ route('password.email') }}"
            error="{{ $errors->first() }}"
            status="{{ session('status') }}"
        >
            @csrf
        </forgot-password>
        </div>
    </div>
</div>
@endsection
