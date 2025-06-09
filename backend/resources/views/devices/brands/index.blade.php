@extends('layouts.admin')

@section('title', 'Brand List')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Device Brands Overview</h2>

    <div class="mb-3">
        <a href="{{ route('device.brands.create') }}" class="btn btn-success">
            + Add New Brand
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Brand Name</th>
                    <th>Device Type</th>
                    <th>Models Count</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($brands as $index => $brand)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $brand->brand_name }}</td>
                        <td>{{ $brand->device_type_name }}</td>
                        <td>{{ $brand->models_count }}</td>
                        <td>
                            <a href="{{ route('device.brands.show', $brand->brand_id) }}" class="btn btn-info btn-sm">Details</a>
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
                        <td colspan="5" class="text-center">No brands found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
