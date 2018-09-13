@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                    @if(!Auth::user()->access_token)
                        <a href="/connect-quickbooks">Activate Quickbooks</a>
                    @else
                        <a href="/api/quickbooks/accounts">Get accounts</a>
                    @endif
                    <div id="root"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
