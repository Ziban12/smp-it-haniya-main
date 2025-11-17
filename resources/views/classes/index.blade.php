@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-chalkboard"></i> Classes</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('employee.classes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Class
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
                        <th>Class ID</th>
                        <th>Class Name</th>
                        <th>Class Level</th>
                        <th>Homeroom Teacher ID</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classes as $class)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $class->class_id }}</span>
                            </td>
                            <td>{{ $class->class_name }}</td>
                            <td>{{ $class->class_level }}</td>
                            <td>{{ $class->homeroom_teacher_id }}</td>
                            <td>
                                <a href="{{ route('employee.classes.edit', $class->class_id) }}" 
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('employee.classes.destroy', $class->class_id) }}" 
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
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2">No classes found. <a href="{{ route('employee.classes.create') }}">Create one</a></p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
