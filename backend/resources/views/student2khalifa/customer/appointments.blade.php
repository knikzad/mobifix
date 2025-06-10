@extends('layouts.customer')

@section('title', 'My Appointments')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">My Repair Appointments</h2>

    @if(count($appointments) > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Appointment Date</th>
                        <th>Time</th>
                        <th>Services</th>
                        <th>Service Method</th>
                        <th>Status</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointments as $appointment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($appointment->date_time)->format('Y-m-d') }}</td>
                            <td>{{ \Carbon\Carbon::parse($appointment->date_time)->format('H:i A') }}</td>
                            <td>{{ $appointment->services }}</td>
                            <td>{{ $appointment->method_name }}</td>
                            <td>
                                <span class="badge {{ $appointment->status == 'Pending' ? 'bg-warning' : 'bg-success' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td><strong>${{ number_format($appointment->total_price, 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-muted">You have no appointments yet.</p>
    @endif
</div>
@endsection
