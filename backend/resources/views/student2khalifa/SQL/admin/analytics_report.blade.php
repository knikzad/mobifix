@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center">Repair Appointment Analytics: Service Method, Customer & Estimated Revenue Insights (Last 30 Days)</h2>

    @if(count($stats) > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>S/N</th>
                    <th>Service Method</th>
                    <th>Total Appointments</th>
                    <th>Appointment AVG Cost (€)</th>
                    <th>Total Appointments Cost (€)</th>
                    <th>Unique Customers</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalAppointmentsSum = 0;
                    $totalRevenueSum = 0;
                    $uniqueCustomersSum = 0;
                @endphp

                @foreach ($stats as $index => $row)
                    @php
                        $totalAppointmentsSum += $row->total_appointments;
                        $totalRevenueSum += $row->total_revenue;
                        $uniqueCustomersSum += $row->unique_customers;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row->method_name }}</td>
                        <td>{{ $row->total_appointments }}</td>
                        <td>{{ number_format($row->avg_repair_price, 2) }}</td>
                        <td>{{ number_format($row->total_revenue, 2) }}</td>
                        <td>{{ $row->unique_customers }}</td>
                    </tr>
                @endforeach

                <!-- Grand Total Row -->
                <tr class="table-secondary fw-bold">
                    <td colspan="2">Grand Total</td>
                    <td>{{ $totalAppointmentsSum }}</td>
                    <td>{{ number_format($totalAppointmentsSum ? $totalRevenueSum / $totalAppointmentsSum : 0, 2) }}</td>
                    <td>{{ number_format($totalRevenueSum, 2) }}</td>
                    <td>{{ $uniqueCustomersSum }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @else
        <div class="alert alert-warning text-center">
            No repair appointment data available.
        </div>
    @endif
</div>
@endsection
