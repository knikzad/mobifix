@extends('layouts.customer')

@section('content')
    <h2>Mustafa – NoSQL Use Case: Unpaid Appointments</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('mustafa.nosql.use_case') }}">
        <label>Select a Customer:</label>
        <select name="user_id" onchange="this.form.submit()">
            <option value="">-- Select --</option>
            @foreach($customers as $c)
                <option value="{{ $c->_id }}" {{ $selectedUserId == $c->_id ? 'selected' : '' }}>
                    {{ $c->first_name }} {{ $c->last_name }}
                </option>
            @endforeach
        </select>
    </form>

    @if(!empty($appointments))
        <h4 class="mt-4">Unpaid Appointments</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total (€)</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appt)
                    <tr>
                        <td>{{ $appt->date_time->toDateTime()->format('Y-m-d H:i:s') }}</td>
                        <td>€{{ number_format($appt->total_price, 2) }}</td>
                        <td>{{ $appt->status }}</td>
                        <td>{{ $appt->payment->payment_status ?? 'Unpaid' }}</td>
                        <td>
                            <a href="{{ route('mustafa.nosql.pay.form', ['appointment_id' => $appt->_id]) }}"
                            class="btn btn-sm btn-primary">Pay</a>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
