@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="text-center mb-4">Payment Analytics Report</h2>

    @if(count($results) > 0)
        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th> {{-- Serial Number --}}
                        <th>Customer Name</th>
                        <th>Contact Method</th>
                        <th>Payment Method</th>
                        <th>Amount</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td> {{-- This auto-generates 1, 2, 3... --}}
                            <td>{{ $row->first_name }} {{ $row->last_name }}</td>
                            <td>{{ $row->preferred_contact_method }}</td>
                            <td>{{ $row->payment_method }}</td>
                            <td><strong>€{{ number_format($row->amount, 2) }}</strong></td>
                            <td>
                                <span class="badge {{ $row->payment_status == 'Paid' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($row->payment_status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info text-center">
            No completed appointments found.
        </div>
    @endif
</div>
@endsection
