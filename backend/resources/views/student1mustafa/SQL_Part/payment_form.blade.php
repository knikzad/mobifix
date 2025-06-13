@extends('layouts.admin')

@section('content')
    <h2>Payment Processing</h2>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Payment for Repair Appointment</h4>
                    </div>
                    <div class="card-body">

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('mustafa.use_case.process') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="appointment_id" class="form-label">Appointment ID</label>
                                <input type="number" name="appointment_id" id="appointment_id" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-select" required>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="PayPal">PayPal</option>
                                    <option value="Cash on Delivery">Cash on Delivery</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount (EUR)</label>
                                <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Submit Payment</button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection
