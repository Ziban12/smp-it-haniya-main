@extends('layouts.app')

@section('title', 'Employees')
@section('page-title', 'Employee Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-users"></i> Employees List</h5>
        <a href="{{ route('employee.employees.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Employee
        </a>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($employees->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Phone</th>
                            <th>Level</th>
                            <th>Entry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td><strong>{{ $employee->employee_id }}</strong></td>
                                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                <td>{{ $employee->username }}</td>
                                <td>{{ $employee->phone ?? '-' }}</td>
                                <td><span class="badge bg-info">{{ $employee->level ?? 'Staff' }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($employee->entry_date)->format('d M Y') ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('employee.employees.edit', $employee->employee_id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('employee.employees.destroy', $employee->employee_id) }}" method="POST" class="d-inline">
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
                <i class="fas fa-info-circle"></i> No employees found. <a href="{{ route('employee.employees.create') }}">Create one now!</a>
            </div>
        @endif
    </div>
</div>
@endsection
