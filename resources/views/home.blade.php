@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @if (Auth::user() && Auth::user()->isVerified())
        <router-view></router-view>
    @else

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">@lang('Dashboard')</div>

                    <div class="card-body text-center">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        @auth
                            @if (!Auth::user()->isVerified())
                                @lang('auth.emailUnverified', ['email' => Auth::user()->email])
                                 <br>
                                Please check your email
                                @lang('or') <a href="{{ route('sendVerifyEmail') }}">@lang('request the verification email again').</a>
                            @endif
                        @else
                            ChatterBox 2.0 - @lang('Please') <a href="{{ route('login') }}">{{ __('log in') }}</a> 
                            @lang('or') <a href="{{ route('register') }}">{{ __('register') }}</a> @lang('to participate') @lang('or')

                            <p class="mt-4">
                                {{ __('auth.Login with') }}
                                <br>
                                <a class="btn btn-sm btn-outline-primary ml-2" href="{{ url('/login/google') }}" role="button">Google</a>
                                <a class="btn btn-sm btn-outline-secondary ml-2" href="{{ url('/login/facebook') }}" role="button">Facebook</a>
                                <a class="btn btn-sm btn-outline-info ml-2" href="{{ url('/login/github') }}" role="button">GitHub</a>
                            </p>
                        @endauth
                        
                        <p class="mt-5">Project Repository: 
                        <a href="https://github.com/matthiku/chatter" target="new">https://github.com/matthiku/chatter</a>
                        <br><span>
                            &copy; 2018 Matthias Kuhs
                            </span>
                        </p>
                        
                    </div>
                </div>
            </div>
        </div>

    @endif
</div>
@endsection
