@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-book"></i> Subjects</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('employee.subjects.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Subject
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
                        <th>Subject ID</th>
                        <th>Subject Name</th>
                        <th>Subject Code</th>
                        <th>Class Level</th>
                        <th>Description</th>
                        <th width="150">Actions</th>
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
                            <td>
                                <a href="{{ route('employee.subjects.edit', $subject->subject_id) }}" 
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('employee.subjects.destroy', $subject->subject_id) }}" 
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
                                <p class="text-muted mt-2">No subjects found. <a href="{{ route('employee.subjects.create') }}">Create one</a></p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
