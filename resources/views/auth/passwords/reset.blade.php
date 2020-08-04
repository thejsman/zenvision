@extends('layouts.app')
@section('title')
   {{ __('page-titles.reset') }} | {{ config('app.name', 'Skote') }}
@endsection
@section('content')
<div>
    
    <div class="account-pages my-5 pt-5">
        <div class="container">
        <reset-password
            submit-url="{{ route('password.update') }}"
            error="{{ $errors->first() }}"
            email="{{ $email ?? old('email') }}"
        >
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
        </reset-password>
        </div>
    </div>
</div>
@endsection
