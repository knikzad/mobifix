@extends('layouts.admin')

@section('title', 'Repair Orders List')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Repair Orders List</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Date & Time</th>
                    <th>Service Method</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Assigned Employee</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($repair_orders as $index => $order)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($order->customer_id)
                                {{ $order->customer_first_name }} {{ $order->customer_last_name }}<br>
                                <small>{{ $order->customer_email }}</small>
                            @else
                                <em>Guest</em>
                            @endif
                        </td>

                        <td>{{ \Carbon\Carbon::parse($order->date_time)->format('Y-m-d H:i') }}</td>
                        <td>{{ $order->method_name ?? 'N/A' }}</td>
                        <td>${{ number_format($order->total_price, 2) }}</td>
                        <td>{{ ucfirst($order->status) }}</td>

                        <td>
                            @if($order->employee_id)
                                {{ $order->employee_first_name }} {{ $order->employee_last_name }}<br>
                                <small>{{ $order->employee_email }}</small>
                            @else
                                <em>Unassigned</em>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.repair-orders.show', $order->appointment_id) }}" class="btn btn-info btn-sm">Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No repair orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
