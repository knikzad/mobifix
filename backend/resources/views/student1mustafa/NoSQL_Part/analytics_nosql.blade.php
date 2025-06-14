@extends('layouts.admin') 
@section('content')
<div class="container mt-4">

    <h2 class="text-center mb-4">Payment Analytics Report</h2>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <!-- Card Payments -->
         <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">💳 Card Payments (Paid)</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Total Paid::</strong> {{ $cardStats['total'] }}</li>
                            <li class="list-group-item"><strong>Total Amount:</strong> €{{ number_format($cardStats['total_collected'], 2) }}</li>
                        </ul>
                    </div>
                </div>
         </div>

        <!-- Cash Payments -->
         <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">💳 Cash Payments (Paid)</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Total Paid::</strong> {{ $cashStats['total'] }}</li>
                            <li class="list-group-item"><strong>Total Amount:</strong> €{{ number_format($cashStats['total_collected'], 2) }}</li>
                        </ul>
                    </div>
                </div>
         </div>

        <!-- Unpaid Appointments -->
        <div class="col-md-4">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">❗ Unpaid Appointments</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Total Unpaid:</strong> {{ $unpaidStats['total'] }}</li>
                            <li class="list-group-item text-muted">Including NULL payments</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    {{-- Appointments Table --}}
    <div class="table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Contact Method</th>
                        <th>Payment Method</th>
                        <th>Amount (€)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($results as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['contact'] }}</td>
                            <td>{{ ucfirst($item['method']) }}</td>
                            <td>{{ number_format($item['amount'], 2) }}</td>
                            <td>
                                @if ($item['status'] === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-warning text-dark">Unpaid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No completed appointments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
