@extends('layouts.customer')

@section('title', 'My Appointments')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">My Repair Appointments (NoSQL)</h2>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

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
                            <td>
                                @foreach(explode(', ', $appointment->services) as $service)
                                    <span class="badge bg-primary">{{ $service }}</span>
                                @endforeach
                            </td>
                            <td>{{ $appointment->method_name ?? 'N/A' }}</td>
                            <td>
                            @php
                                $statusClass = match($appointment->status) {
                                    'booked' => 'bg-primary',
                                    'confirmed' => 'bg-info',
                                    'in-progress' => 'bg-warning',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            @endphp

                            <span class="badge {{ $statusClass }}">
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
