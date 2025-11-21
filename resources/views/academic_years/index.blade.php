@extends('layouts.app')

@section('title', 'Academic Years')
@section('page-title', 'Academic Year Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Academic Year List</h5>

        <a href="{{ route('employee.academic.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Academic Year
        </a>
    </div>

    <div class="card-body">

        @if ($academicYears->count() > 0)
            <div class="table-responsive">
                <table id="academicYearTable" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Academic Year ID</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th class="text-center" width="140">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($academicYears as $academicYear)
                            <tr>
                                <td>
                                    <span class="badge bg-info">{{ $academicYear->academic_year_id }}</span>
                                </td>

                                <td>{{ $academicYear->start_date ? \Carbon\Carbon::parse($academicYear->start_date)->format('d M Y') : '-' }}</td>
                                <td>{{ $academicYear->end_date ? \Carbon\Carbon::parse($academicYear->end_date)->format('d M Y') : '-' }}</td>

                                <td>{{ $academicYear->semester }}</td>

                                <td>
                                    @if ($academicYear->status === 'Active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $academicYear->status }}</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">

                                        <!-- Edit -->
                                        <a href="{{ route('employee.academic.edit', $academicYear->academic_year_id) }}"
                                            class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Delete -->
                                        <form action="{{ route('employee.academic.destroy-academic', $academicYear->academic_year_id) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="btn btn-danger delete-confirm"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No academic years found.
                <a href="{{ route('employee.academic.create-academic') }}">Create one now!</a>
            </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DATATABLE -->
<script>
$(document).ready(function () {
    $('#academicYearTable').DataTable({
        ordering: true,
        pageLength: 10,
    });
});
</script>

<!-- SWEETALERT SUCCESS -->
@if (session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Sukses!',
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
    title: 'Gagal!',
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
        title: 'Yakin hapus?',
        text: "Data academic year akan dihapus dari sistem!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#0d6efd',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>

@endpush
