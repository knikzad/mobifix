@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Pending Cash Payments</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Amount (€)</th>
                <th>Payment Method</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingPayments as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['first_name'] }} {{ $item['last_name'] }}</td>
                    <td>{{ number_format($item['amount'], 2) }}</td>
                    <td>{{ ucfirst($item['payment_method']) }}</td>
                    <td>
                        <form action="{{ route('mustafa.nosql.confirm_payment') }}" method="POST">
                            @csrf
                            <input type="hidden" name="appointment_id" value="{{ $item['appointment_id'] }}">
                            <button type="submit" class="btn btn-success btn-sm">Confirm Payment</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
