@extends('layouts.admin')

@section('content')
    <h2>Mustafa – NoSQL Analytics Report</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th> {{-- Serial Number --}}
                <th>Customer Name</th>
                <th>Contact Method</th>
                <th>Payment Method</th>
                <th>Amount (€)</th>
                <th>Payment Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $r)
                <tr>
                    <td>{{ $loop->iteration }}</td> {{-- This auto-generates 1, 2, 3... --}}
                    <td>{{ $r['name'] }}</td>
                    <td>{{ $r['contact'] }}</td>
                    <td>{{ $r['method'] }}</td>
                    <td>€{{ number_format($r['amount'], 2) }}</td>
                    <td>{{ $r['status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
