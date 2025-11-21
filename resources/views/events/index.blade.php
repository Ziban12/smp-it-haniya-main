@extends('layouts.app')

@section('title', 'Events')
@section('page-title', 'Events Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Events List</h5>
        <a href="{{ route('employee.events.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Event
        </a>
    </div>

    <div class="card-body">

        @if ($events->count() > 0)
            <div class="table-responsive">
                <table id="eventsTable" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Event ID</th>
                            <th>Event Name</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Tags</th>
                            <th>Created By</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($events as $event)
                            <tr>
                                <td><span class="badge bg-info">{{ $event->event_id }}</span></td>
                                <td>{{ $event->event_name }}</td>
                                <td>{{ $event->location }}</td>
                                <td>
                                    @if ($event->status === 'Ongoing')
                                        <span class="badge bg-danger">{{ $event->status }}</span>
                                    @elseif ($event->status === 'Upcoming')
                                        <span class="badge bg-warning">{{ $event->status }}</span>
                                    @elseif ($event->status === 'Completed')
                                        <span class="badge bg-success">{{ $event->status }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $event->status }}</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-info">{{ $event->tag_count ?? 0 }} Tags</span>
                                </td>

                                <td>{{ $event->created_by }}</td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">

                                        <!-- Manage Tags -->
                                        <a href="{{ route('employee.events.create', $event->event_id) }}"
                                            class="btn btn-info" title="Manage Tags">
                                            <i class="fas fa-tags"></i>
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('employee.events.edit', $event->event_id) }}"
                                            class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Delete -->
                                        <form action="{{ route('employee.events.destroy', $event->event_id) }}"
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
                <i class="fas fa-info-circle"></i> No events found.
                <a href="{{ route('employee.events.create') }}">Create one now!</a>
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
    $('#eventsTable').DataTable({
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
        text: "Data event akan dihapus dari sistem!",
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
