@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @auth
                        You are logged in! 
                        <p>
                            Start chatting.
                            <button class="btn btn-primary float-right">New Chat</button>
                        </p>
                        Your E-Mail address {{ auth()->user()->isVerified() ? 'is verified' : 'has not been verified yet. Please check your email.' }}
                    @else
                        ChatterBox 2.0 - Please log in or register to participate
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
