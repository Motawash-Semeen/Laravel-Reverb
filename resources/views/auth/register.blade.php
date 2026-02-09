@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="container" style="margin-top: 80px;">
    <div class="card" style="max-width: 450px; margin: 0 auto;">
        <h2 class="text-center" style="margin-bottom: 25px; color: #00d2ff;">Create Account</h2>

        @if($errors->any())
        <div class="error-list">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">Register</button>
        </form>

        <p class="text-center mt-3">Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </div>
</div>
@endsection
