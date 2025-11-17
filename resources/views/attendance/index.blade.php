@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Attendance Records</h5>
                    <a href="{{ route('employee.attendance.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Record Attendance
                    </a>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendances as $attendance)
                                    <tr>
                                        <td><span class="badge bg-info">{{ $attendance->student_id }}</span></td>
                                        <td>{{ $attendance->first_name }} {{ $attendance->last_name }}</td>
                                        <td>{{ $attendance->class_name }}</td>
                                        <td>{{ date('d M Y', strtotime($attendance->attendance_date)) }}</td>
                                        <td>
                                            @if ($attendance->status === 'Present')
                                                <span class="badge bg-success">Present</span>
                                            @elseif ($attendance->status === 'Absent')
                                                <span class="badge bg-danger">Absent</span>
                                            @elseif ($attendance->status === 'Late')
                                                <span class="badge bg-warning">Late</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $attendance->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $attendance->notes ? substr($attendance->notes, 0, 30) . '...' : '-' }}</td>
                                        <td>
                                            <form action="{{ route('employee.attendance.destroy', $attendance->attendance_id) }}" 
                                                  method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete"
                                                        onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox"></i> No attendance records found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
