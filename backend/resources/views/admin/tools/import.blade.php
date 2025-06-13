@extends('layouts.admin')

@section('title', 'Import Random Data')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header -->
            <div class="text-center mb-4">
                <h2>Database Import Tool</h2>
                <p class="text-muted">Click the button below to import randomized test data into the database.</p>
            </div>

            <!-- Card -->
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <button id="import-btn" class="btn btn-primary px-4 py-2">Import Random Data</button>
                    <div id="alert-container" class="mt-4"></div>
                </div>
            </div>
        <div class="d-flex justify-content-center mt-4 mb-4">
            <form action="{{ route('mongo-migrate') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger mt-3">
                    🔁 Migrate SQL → MongoDB
                </button>
            </form>
        </div>

            <!-- Footer -->
            <div class="text-center mt-4">
                <small class="text-muted">Mobifix Development &mdash; Import Random Data Interface</small>
            </div>
        </div>
    </div>
</div>
@endsection