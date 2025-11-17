@extends('layouts.app')

@section('title', 'Students')
@section('page-title', 'Student Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-book"></i> Students List</h5>
        <a href="{{ route('employee.students.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Student
        </a>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($students->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>NIS</th>
                            <th>Gender</th>
                            <th>Father Name</th>
                            <th>Entry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td><strong>{{ $student->student_id }}</strong></td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->nis }}</td>
                                <td>{{ $student->gender === 'M' ? 'Male' : ($student->gender === 'F' ? 'Female' : '-') }}</td>
                                <td>{{ $student->father_name ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($student->entry_date)->format('d M Y') ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('employee.students.edit', $student->student_id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('employee.students.destroy', $student->student_id) }}" method="POST" class="d-inline">
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
                <i class="fas fa-info-circle"></i> No students found. <a href="{{ route('employee.students.create') }}">Create one now!</a>
            </div>
        @endif
    </div>
</div>
@endsection
