@extends('layouts.app')

@section('title', 'Students')
@section('page-title', 'Student Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-book"></i> Students List</h5>
        <a href="{{ route('employee.students.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Student
        </a>
    </div>

    <div class="card-body">
        @if ($students->count() > 0)
            <div class="table-responsive">
                <table id="studentsTable" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>NIS</th>
                            <th>Gender</th>
                            <th>Father Name</th>
                            <th>Entry Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td><strong>{{ $student->student_id }}</strong></td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->nis }}</td>
                                <td>
                                    {{ $student->gender === 'M' ? 'Male' : ($student->gender === 'F' ? 'Female' : '-') }}
                                </td>
                                <td>{{ $student->father_name ?? '-' }}</td>
                                <td>
                                    {{ $student->entry_date ? \Carbon\Carbon::parse($student->entry_date)->format('d M Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('employee.students.edit', $student->student_id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('employee.students.destroy', $student->student_id) }}"
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
                <i class="fas fa-info-circle"></i> No students found.
                <a href="{{ route('employee.students.create') }}">Create one now!</a>
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
    $('#studentsTable').DataTable({
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
        text: "Data akan dihapus dari sistem!",
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
