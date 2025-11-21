@extends('layouts.app')

@section('title', 'Subjects')
@section('page-title', 'Subject Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-book"></i> Subject List</h5>

        <a href="{{ route('employee.subjects.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Subject
        </a>
        
    </div>

    <div class="card-body">

        {{-- ALERT BIASA (opsional, boleh hapus kalau sudah pakai Swal semua) --}}
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (count($subjects) > 0)

            <div class="table-responsive">
                <table id="subjectsTable" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Subject ID</th>
                            <th>Subject Name</th>
                            <th>Subject Code</th>
                            <th>Class Level</th>
                            <th>Description</th>
                            <th class="text-center" width="140">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($subjects as $subject)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $subject->subject_id }}</span>
                                </td>
                                <td>{{ $subject->subject_name }}</td>
                                <td>{{ $subject->subject_code }}</td>
                                <td>{{ $subject->class_level }}</td>
                                <td>
                                    <small>{{ Str::limit($subject->description, 50) }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">

                                        {{-- Edit --}}
                                        <a href="{{ route('employee.subjects.edit', $subject->subject_id) }}"
                                           class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('employee.subjects.destroy', $subject->subject_id) }}"
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
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">No subjects found.
                                        <a href="{{ route('employee.subjects.create') }}">
                                            Create one
                                        </a>
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        @else

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                No subjects found.
                <a href="{{ route('employee.subjects.create') }}">Create one now!</a>
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
    $('#subjectsTable').DataTable({
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
