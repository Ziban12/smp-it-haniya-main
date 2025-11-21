@extends('layouts.app')

@section('title', 'Employees')
@section('page-title', 'Employee Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-users"></i> Employees List</h5>
        <a href="{{ route('employee.employees.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Employee
        </a>
    </div>
    <div class="card-body">

        @if ($employees->count() > 0)
            <div class="table-responsive">
                <table id="employeesTable" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Employee ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Username</th>
        
            <th>Gender</th>
            <th>Birth Place</th>
            <th>Birth Date</th>
            <th>Address</th>
            <th>Phone</th>
           
            <th>Level</th>
            <th>Status</th>
            <th>Profile Photo</th>
          
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($employees as $employee)
            <tr>
                <td><strong>{{ $employee->employee_id }}</strong></td>
                <td>{{ $employee->first_name }}</td>
                <td>{{ $employee->last_name }}</td>
                <td>{{ $employee->username }}</td>
               
                <td>{{ $employee->gender ?? '-' }}</td>
                <td>{{ $employee->birth_place ?? '-' }}</td>
                <td>
                    {{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->format('d M Y') : '-' }}
                </td>
                <td>{{ $employee->address ?? '-' }}</td>
                <td>{{ $employee->phone ?? '-' }}</td>
                
                <td>
                    <span class="badge bg-info">{{ $employee->level ?? 'Staff' }}</span>
                </td>
                <td>
                    @if ($employee->status == 'Active')
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </td>
                <td>
                    @if($employee->profile_photo)
                        <img src="{{ asset('storage/' . $employee->profile_photo) }}" 
                             alt="Photo"
                             width="40" class="rounded">
                    @else
                        -
                    @endif
                </td>
               

                <td class="text-center">

                    <a href="{{ route('employee.employees.edit', $employee->employee_id) }}"
                        class="btn btn-warning btn-sm mb-1">
                        <i class="fas fa-edit"></i> Edit
                    </a>

                    <form action="{{ route('employee.employees.destroy', $employee->employee_id) }}"
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
                <i class="fas fa-info-circle"></i> No employees found.
                <a href="{{ route('employee.employees.create') }}">Create one now!</a>
            </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<!-- ============================
     Javascript Library
==============================-->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ============================
     DataTables Setup
==============================-->
<script>
    $(document).ready(function () {
        $('#employeesTable').DataTable({
            ordering: true,
            pageLength: 10
        });
    });
</script>

<!-- ============================
     SweetAlert Notification
==============================-->
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


@if (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session("error") }}',
        showConfirmButton: true
    });
</script>
@endif

<!-- ============================
     SweetAlert Delete Confirmation
==============================-->
<script>
$('.delete-confirm').on('click', function(event) {
    event.preventDefault();
    let form = $(this).closest('form');

    Swal.fire({
        title: 'Yakin hapus?',
        text: "Status akan berubah menjadi Inactive!",
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
