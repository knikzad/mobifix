@extends('layouts.admin')

@section('title', 'Khalifa Use Case')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Select a User</h2>

    <div class="mb-3">
        <p>Please select a user to proceed:</p>
    </div>

    <form action="{{ route('nosql.use_case.selectUser') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="userSelect" class="form-label">Choose a User</label>
            <select id="userSelect" name="user_id" class="form-select" required>
                <option value="">...</option>
                @foreach ($users as $user)
                    <option value="{{ $user->_id }}">
                        {{ $user->first_name }} {{ $user->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Proceed</button>
    </form>
</div>
@endsection
