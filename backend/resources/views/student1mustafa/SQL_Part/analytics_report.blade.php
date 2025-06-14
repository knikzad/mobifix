@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="text-center mb-4">Payment Analytics Report</h2>

    @if(isset($cardStats) && isset($cashStats) && isset($unpaidStats))
        <div class="row mb-4">
            {{-- Card Payments --}}
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">💳 Card Payments (Paid)</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Total:</strong> {{ $cardStats->total }}</li>
                            <li class="list-group-item"><strong>Total Amount:</strong> €{{ number_format($cardStats->total_collected ?? 0, 2) }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Cash Payments --}}
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">💵 Cash Payments (Paid)</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Total:</strong> {{ $cashStats->total }}</li>
                            <li class="list-group-item"><strong>Total Amount:</strong> €{{ number_format($cashStats->total_collected ?? 0, 2) }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Unpaid Appointments --}}
            <div class="col-md-4">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">❗ Unpaid Appointments</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Total Unpaid:</strong> {{ $unpaidStats->total }}</li>
                            <li class="list-group-item text-muted">Including NULL payments</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif



    @if(count($results) > 0)
        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
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
                            <td>{{ $loop->iteration }}</td>
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
