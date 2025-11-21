@extends('layouts.app')

@section('title', 'Classes')
@section('page-title', 'Class Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-chalkboard"></i> Class List</h5>

        <a href="{{ route('employee.classes.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Class
        </a>
    </div>

    <div class="card-body">

        @if ($classes->count() > 0)

            <div class="table-responsive">
                <table id="classesTable" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Class ID</th>
                            <th>Class Name</th>
                            <th>Class Level</th>
                            <th>Homeroom Teacher</th>
                            <th class="text-center" width="140">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($classes as $class)
                            <tr>
                                <td>
                                    <span class="badge bg-info">{{ $class->class_id }}</span>
                                </td>

                                <td>{{ $class->class_name }}</td>
                                <td>{{ $class->class_level }}</td>
                                <td>{{ $class->homeroom_teacher_id }}</td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">

                                        {{-- Edit --}}
                                        <a href="{{ route('employee.classes.edit', $class->class_id) }}"
                                           class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('employee.classes.destroy', $class->class_id) }}"
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
                <i class="fas fa-info-circle"></i>
                No classes found.
                <a href="{{ route('employee.classes.create') }}">Create one now!</a>
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

{{-- DATATABLE --}}
<script>
$(document).ready(function () {
    $('#classesTable').DataTable({
        ordering: true,
        pageLength: 10,
    });
});
</script>

{{-- SWEETALERT SUCCESS --}}
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

{{-- SWEETALERT ERROR --}}
@if (session('error'))
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '{{ session("error") }}'
});
</script>
@endif

{{-- SWEETALERT DELETE CONFIRM --}}
<script>
$('.delete-confirm').on('click', function(e) {
    e.preventDefault();
    let form = $(this).closest('form');

    Swal.fire({
        title: 'Yakin hapus?',
        text: "Data class akan dihapus dari sistem!",
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
