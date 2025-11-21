@extends('layouts.app')

@section('title', 'Attendance Records')
@section('page-title', 'Attendance Management')

@push('styles')
<link rel="stylesheet" 
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-check-circle"></i> Attendance Records
        </h5>
        <a href="{{ route('employee.attendance.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Record Attendance
        </a>
    </div>

    <div class="card-body">

        <div class="table-responsive">
            <table id="attendanceTable" class="table table-hover table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($attendances as $attendance)
                        <tr>
                            <td>
                                <span class="badge bg-info">{{ $attendance->student_id }}</span>
                            </td>

                            <td>
                                {{ $attendance->first_name }} {{ $attendance->last_name }}
                            </td>

                            <td>{{ $attendance->class_name }}</td>

                            <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') }}</td>

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

                            <td>
                                {{ $attendance->notes ? Str::limit($attendance->notes, 40) : '-' }}
                            </td>

                            <td class="text-center">
                                <form action="{{ route('employee.attendance.destroy', $attendance->attendance_id) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm delete-confirm">
                                        <i class="fas fa-trash"></i> Delete
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

@endsection

@push('scripts')

{{-- JQUERY --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- DATATABLES --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

{{-- SWEET ALERT --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- INIT DATATABLE -->
<script>
$(document).ready(function () {
    $('#attendanceTable').DataTable({
        ordering: true,
        pageLength: 10,
        responsive: true
    });
});
</script>

<!-- SWEETALERT SUCCESS -->
@if (session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: '{{ session("success") }}',
    timer: 2000,
    showConfirmButton: false
});
</script>
@endif

<!-- SWEETALERT ERROR -->
@if (session('error'))
<script>
Swal.fire({
    icon: 'error',
    title: 'Error!',
    text: '{{ session("error") }}'
});
</script>
@endif

<!-- DELETE CONFIRM -->
<script>
$('.delete-confirm').on('click', function(e) {
    e.preventDefault();
    let form = $(this).closest('form');

    Swal.fire({
        title: 'Are you sure?',
        text: "This attendance record will be deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#0d6efd',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>

@endpush
