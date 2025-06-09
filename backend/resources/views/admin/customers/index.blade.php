@extends('layouts.admin')

@section('title', 'Customer List')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Customer List</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $index => $customer) 
                    <tr>
                        <td>{{ $index + 1 }}</td> 
                        <td>{{ $customer->first_name }}</td>
                        <td>{{ $customer->last_name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>
                            <!-- Action Buttons -->
                            <a href="{{ route('admin.customers.show', $customer->user_id) }}" class="btn btn-info btn-sm">Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
