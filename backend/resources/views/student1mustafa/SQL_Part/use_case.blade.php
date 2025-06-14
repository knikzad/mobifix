@extends('layouts.customer')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Payment per Appointment</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Dropdown Form -->
    <form method="GET" action="{{ route('mustafa.use_case.page') }}">
        <div class="mb-3">
            <label for="user_id" class="form-label">Select Customer:</label>
            <select name="user_id" id="user_id" class="form-select" required>
                <option value="">-- Select --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->user_id }}" >
                        {{ $customer->first_name }} {{ $customer->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Load Appointments</button>
    </form>

    <!-- Appointments Table -->
    @if(!empty($appointments))
        <h4 class="mt-5">Your Appointments</h4>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>#</th> {{-- Serial Number --}}
                    <th>Date/Time</th>
                    <th>Appointment Status</th>
                    <th>Total Price (€)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appt)
                    <tr>
                        <td>{{ $loop->iteration }}</td> {{-- This auto-generates 1, 2, 3... --}}
                        <td>{{ \Carbon\Carbon::parse($appt->date_time)->format('Y-m-d H:i') }}</td>
                        <td>{{ $appt->status }}</td>
                        <td>{{ number_format($appt->total_price, 2) }}</td>
                        <td>
                            <a href="{{ route('mustafa.use_case.pay_form', $appt->appointment_id) }}" class="btn btn-success btn-sm">
                                Pay
                            </a>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
