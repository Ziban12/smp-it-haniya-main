@extends('layouts.app')

@section('title', 'Teachers')
@section('page-title', 'Teacher Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-chalkboard-user"></i> Teachers List</h5>
        <a href="{{ route('employee.teachers.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Teacher
        </a>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($teachers->count() > 0)

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Teacher ID</th>
                            <th>Full Name</th>
                            <th>NPK</th>
                            <th>Phone</th>
                            <th>Level</th>
                            <th>Entry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teachers as $teacher)
                            <tr>
                                <td><strong>{{ $teacher->teacher_id }}</strong></td>
                                <td>{{ $teacher->first_name }} {{ $teacher->last_name }}</td>
                                <td>{{ $teacher->npk }}</td>
                                <td>{{ $teacher->phone ?? '-' }}</td>
                                <td><span class="badge bg-success">{{ $teacher->level ?? 'Teacher' }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($teacher->entry_date)->format('d M Y') ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('employee.teachers.edit', $teacher->teacher_id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('employee.teachers.destroy', $teacher->teacher_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No teachers found. <a href="{{ route('employee.teachers.create') }}">Create one now!</a>
            </div>
        @endif
    </div>
</div>
@endsection
