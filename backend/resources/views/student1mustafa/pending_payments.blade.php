@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="text-center">Pending Payments</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(count($pendingPayments) > 0)
        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Customer Name</th>
                        <th>Amount (€)</th>
                        <th>Payment Method</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingPayments as $payment)
                        <tr>
                            <td>{{ $payment->first_name }} {{ $payment->last_name }}</td>
                            <td><strong>€{{ number_format($payment->amount, 2) }}</strong></td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>
                                <form method="POST" action="{{ route('mustafa.confirmPayment') }}">
                                    @csrf
                                    <input type="hidden" name="appointment_id" value="{{ $payment->appointment_id }}">
                                    <button type="submit" class="btn btn-success">Confirm Payment</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info text-center">
            No pending payments found.
        </div>
    @endif
</div>
@endsection
