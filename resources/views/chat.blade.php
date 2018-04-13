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

                    <chat-log
                        :messages="messages"
                        ></chat-log>

                    <chat-composer
                            v-on:messagesent="addMessage"
                            :user="user"
                        ></chat-composer>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
