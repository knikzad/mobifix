@extends('layouts.admin')

@section('title', 'Models')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Models List</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">← Back</a>
        <a href="{{ route('device.models.create') }}" class="btn btn-success">
            + Add New Model
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Model Name</th>
                    <th>Brand</th>
                    <th>Device Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($models as $index => $model)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $model->model_name }}</td>
                        <td>{{ $model->brand_name }}</td>
                        <td>{{ $model->type_name }}</td>
                        <td>
                            <a href="{{ route('device.models.edit', $model->model_id) }}" class="btn btn-warning btn-sm">Edit</a>

                            <form action="{{ route('device.models.destroy', $model->model_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this model?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No models found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
