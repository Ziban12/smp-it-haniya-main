@extends('layouts.app')

@section('title', 'Teachers')
@section('page-title', 'Teacher Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-chalkboard-user"></i> Teachers List</h5>
        <a href="{{ route('employee.teachers.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Teacher
        </a>
    </div>
    <div class="card-body">

        @if ($teachers->count() > 0)

            <div class="table-responsive">
                <table id="teachersTable" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Teacher ID</th>
                            <th>Full Name</th>
                            <th>NPK</th>
                            <th>Phone</th>
                            <th>Level</th>
                            <th>Entry Date</th>
                            <th class="text-center">Actions</th>
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
                                <td>
                                    {{ $teacher->entry_date ? \Carbon\Carbon::parse($teacher->entry_date)->format('d M Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('employee.teachers.edit', $teacher->teacher_id) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('employee.teachers.destroy', $teacher->teacher_id) }}"
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
                        @endforeach
                    </tbody>
                </table>
            </div>

        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No teachers found.
                <a href="{{ route('employee.teachers.create') }}">Create one now!</a>
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
    $('#teachersTable').DataTable({
        ordering: true,
        pageLength: 10
    });
});
</script>

<!-- SWEETALERT SUKSES -->
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

<!-- KONFIRMASI DELETE -->
<script>
$('.delete-confirm').on('click', function(e) {
    e.preventDefault();
    let form = $(this).closest('form');

    Swal.fire({
        title: 'Yakin hapus?',
        text: "Data hanya berubah menjadi tidak aktif!",
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
