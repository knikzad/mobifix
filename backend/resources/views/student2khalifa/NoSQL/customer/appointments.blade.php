@extends('layouts.customer')

@section('title', 'My Appointments')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">My Repair Appointments (NoSQL)</h2>

    @if(count($appointments) > 0)
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
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
                            <td>{{ $appointment->date_time->toDateTime()->format('Y-m-d') }}</td>
                            <td>{{ $appointment->date_time->toDateTime()->format('h:i A') }}</td>
                            <td>{{ $appointment->services }}</td>
                            <td>{{ $appointment->method_name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge 
                                    @if($appointment->status === 'Pending') bg-warning
                                    @elseif($appointment->status === 'Completed') bg-success
                                    @elseif($appointment->status === 'Canceled') bg-danger
                                    @else bg-secondary @endif">
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
