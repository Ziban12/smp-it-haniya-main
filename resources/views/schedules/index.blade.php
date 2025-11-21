@extends('layouts.app')

@section('title', 'Schedules')
@section('page-title', 'Class Schedule Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-clock"></i> Class Schedules</h5>
        <a href="{{ route('employee.schedules.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Schedule
        </a>
    </div>

    <div class="card-body">

        <div class="table-responsive">
            <table id="scheduleTable" class="table table-hover table-bordered align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Academic Year</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($schedules as $index => $schedule)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $schedule->class_name }}</strong></td>
                            <td>{{ $schedule->subject_name }}</td>
                            <td>{{ $schedule->teacher_name }}</td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $schedule->day }}</span>
                            </td>
                            <td>{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                            <td>{{ $schedule->academic_year_id }}</td>

                            <td class="text-center">

                                <a href="{{ route('employee.schedules.edit', $schedule->schedule_id) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <form action="{{ route('employee.schedules.destroy', $schedule->schedule_id) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger btn-sm delete-confirm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">
                                <i class="fas fa-inbox"></i> No schedules found
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- DATATABLES --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

{{-- SWEET ALERT --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- INITIALIZE DATATABLE -->
<script>
$(document).ready(function () {
    $('#scheduleTable').DataTable({
        ordering:  true,
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

<!-- DELETE CONFIRMATION -->
<script>
$('.delete-confirm').on('click', function(e) {
    e.preventDefault();
    let form = $(this).closest('form');

    Swal.fire({
        title: 'Are you sure?',
        text: "This schedule will be permanently deleted!",
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
