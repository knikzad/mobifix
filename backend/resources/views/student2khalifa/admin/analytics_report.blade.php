@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center">Repair Appointments Analytics Report: Employee Performance & Revenue (Past Month)</h2>

    @if(count($stats) > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>S/N</th>
                    <th>Responsible Employee</th>
                    <th>Service Method</th>
                    <th>Total Appointments</th>
                    <th>Estimated AVG Repair Cost (€)</th>
                    <th> Total Estimated Revenue (€)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stats as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row->employee_first_name }} {{ $row->employee_last_name }}</td>
                        <td>{{ $row->method_name }}</td>
                        <td>{{ $row->completed_appointments }}</td>
                        <td>{{ number_format($row->avg_repair_price, 2) }}</td>
                        <td>{{ number_format($row->total_revenue, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <div class="alert alert-warning text-center">
            No completed repair appointment data available.
        </div>
    @endif
</div>
@endsection
