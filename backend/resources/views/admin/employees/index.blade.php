@extends('layouts.admin')

@section('title', 'Employee List')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Employee List</h2>

    <!-- Add New Employee Button -->
    <div class="mb-3">
        <a href="{{ route('admin.employees.create') }}" class="btn btn-success">
            + Add New Employee
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                    <tr>
                        <td>{{ $employee->first_name }}</td>
                        <td>{{ $employee->last_name }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->phone }}</td>
                        <td>
                            <!-- Action Buttons -->
                            <a href="{{ route('admin.employees.show', $employee->user_id) }}" class="btn btn-info btn-sm">Details</a>
                            <a href="{{ route('admin.employees.edit', $employee->user_id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('admin.employees.destroy', $employee->user_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
