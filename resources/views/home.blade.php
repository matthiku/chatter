@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @if (Auth::user() && Auth::user()->isVerified())
        <chat-rooms></chat-rooms>
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
                            @lang('or') <a href="{{ route('register') }}">{{ __('register') }}</a> @lang('to participate')
                        @endauth
                        
                        <p class="mt-5">Project homepage: 
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
