@extends('layouts.customer')

@section('content')
<div class="container mt-4">
    <h2>Pay for Your Appointment</h2>

    <form method="POST" action="{{ route('mustafa.use_case.pay') }}">
        @csrf
        <input type="hidden" name="appointment_id" value="{{ $appointment->appointment_id }}">

        <div class="mb-3">
            <label>Date/Time</label>
            <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($appointment->date_time)->format('Y-m-d H:i') }}" disabled>
        </div>

        <div class="mb-3">
            <label>Total Amount To Pay (€)</label>
            <input type="text" class="form-control" value="{{ number_format($appointment->total_price, 2) }}" disabled>
        </div>

        <div class="mb-3">
            <label for="payment_method">Payment Method</label>
            <select name="payment_method" id="payment_method" class="form-select" required onchange="toggleCardFields()"> 
            <option value="">Select method</option>
            <option value="card">Credit Card</option>
                <option value="cash">Cash</option>
            </select>
        </div>

        {{-- Hidden card details fields --}}
        <div id="cardFields" class="d-none">
            <div class="mb-3">
                <label for="card_number">Card Number</label>
                <input type="number" name="card_number" id="card_number" class="form-control" placeholder="**** **** **** 1234">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label for="expiration_date">Expiration Date</label>
                    <input type="text" name="expiration_date" id="expiration_date" class="form-control" placeholder="MM/YY">
                </div>
                <div class="col-md-6">
                    <label for="cvv">CVV</label>
                    <input type="password" name="cvv" id="cvv" class="form-control" placeholder="123">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Pay Now</button>
    </form>
</div>

<script>
    function toggleCardFields() {
        const paymentMethod = document.getElementById('payment_method').value;
        const cardFields = document.getElementById('cardFields');

        if (paymentMethod === 'card') {
            cardFields.classList.remove('d-none');
        } else {
            cardFields.classList.add('d-none');
        }
    }
</script>
@endsection
