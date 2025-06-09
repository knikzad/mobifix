@extends('layouts.admin')

@section('title', 'Device Type List')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Electronic Device Types Overview</h2>

    <!-- Add New Device Type Button -->
    <div class="mb-3">
        <a href="{{ route('device.types.create') }}" class="btn btn-success">
            + Add New Type of Device
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Brands Count</th>
                    <th>Models Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($device_types as $index => $type) 
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $type->type_name }}</td>
                        <td>{{ $type->brands_count }}</td>
                        <td>{{ $type->models_count }}</td>
                        <td>
                            <!-- Action Buttons -->
                            <a href="{{ route('device.types.brands', $type->device_type_id) }}" class="btn btn-info btn-sm">Show Brands</a>
                            <a href="{{ route('device.types.edit', $type->device_type_id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('device.types.destroy', $type->device_type_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this device type?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No device type found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
