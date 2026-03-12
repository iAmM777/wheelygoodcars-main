@extends('layouts.app')

@section('content')

    <div class="fullheight">
        <div class="card p-4" style="min-width: 50%;">
            <div class="card-body">
                <h5 class="card-title mb-3">Inloggen</h5>
                @include('layouts.error')
                <form action="{{ route('login.do') }}" method="POST">
                    @csrf
                    <div class="mb-3 row">
                        <label for="email" class="col-form-label col-sm-4">E-mailadres:</label>
                        <div class="col-sm-8">
                        <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com"></div>
                    </div>
                    <div class="mb-3 row">
                        <label for="password" class="col-form-label col-sm-4">Wachtwoord:</label>
                        <div class="col-sm-8">
                        <input type="password" name="password" class="form-control" id="password"></div>
                    </div>
                    <div class="mb-3">
                        <input type="submit" class="btn btn-primary text-dark" value="Inloggen">
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
