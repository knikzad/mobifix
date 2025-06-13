@extends('layouts.admin')

@section('content')
    <h2>Mustafa – Pay Appointment (MongoDB)</h2>

    <p><strong>Appointment ID:</strong> {{ $appointment->_id }}</p>
    <p><strong>Total:</strong> €{{ number_format($appointment->total_price, 2) }}</p>
    <p><strong>Status:</strong> {{ $appointment->status }}</p>

    <form method="POST" action="{{ route('mustafa.nosql.pay.submit') }}">
        @csrf
        <input type="hidden" name="appointment_id" value="{{ $appointment->_id }}">

        <div class="mb-3">
            <label>Payment Method:</label><br>
            <label><input type="radio" name="payment_method" value="Card" required> Card</label>
            <label><input type="radio" name="payment_method" value="Cash" required> Cash</label>
        </div>

        <button type="submit" class="btn btn-success">Confirm Payment</button>
    </form>
@endsection
