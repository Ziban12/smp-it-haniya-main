@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-calendar-alt"></i> Academic Years</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('employee.academic_years.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Academic Year
            </a>
        </div>
    </div>

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

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Academic Year ID</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($academicYears as $academicYear)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $academicYear->academic_year_id }}</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($academicYear->start_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($academicYear->end_date)->format('d M Y') }}</td>
                            <td>{{ $academicYear->semester }}</td>
                            <td>
                                @if ($academicYear->status === 'Active')
                                    <span class="badge bg-success">{{ $academicYear->status }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $academicYear->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('employee.academic_years.edit', $academicYear->academic_year_id) }}" 
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('employee.academic_years.destroy', $academicYear->academic_year_id) }}" 
                                      method="POST" style="display:inline;" 
                                      onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2">No academic years found. <a href="{{ route('employee.academic_years.create') }}">Create one</a></p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
