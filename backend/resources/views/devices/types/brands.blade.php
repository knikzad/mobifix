@extends('layouts.admin')

@section('title', 'Brands for ' . $device->type_name)

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Brands for Device Type: <strong>{{ $device->type_name }}</strong></h2>

    <div class="mb-3">
        <a href="{{ route('device.types.index') }}" class="btn btn-secondary">← Back to Device Types</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Brand Name</th>
                    <th>Models Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($brands as $index => $brand)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $brand->brand_name }}</td>
                        <td>{{ $brand->models_count }}</td>
                        <td>
                            <a href="{{ route('device.brands.models', ['brand_id' => $brand->brand_id, 'device_type_id' => $device->device_type_id]) }}" class="btn btn-info btn-sm">
                                Show Models
                            </a>
                            <a href="{{ route('device.brands.edit', $brand->brand_id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('device.brands.destroy', $brand->brand_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this brand?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No brands found for this device type.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
