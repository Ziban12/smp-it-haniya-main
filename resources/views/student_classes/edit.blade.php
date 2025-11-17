@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-edit"></i> Edit Student Class Assignment</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('employee.student_classes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Assignments
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('employee.student_classes.update', $studentClass->student_class_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Student ID</label>
                        <input type="text" class="form-control" value="{{ $studentClass->student_id }}" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Assignment ID</label>
                        <input type="text" class="form-control" value="{{ $studentClass->student_class_id }}" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                        <select class="form-select @error('class_id') is-invalid @enderror" 
                                id="class_id" name="class_id" required>
                            <option value="">-- Select Class --</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->class_id }}" {{ old('class_id', $studentClass->class_id) == $class->class_id ? 'selected' : '' }}>
                                    {{ $class->class_name }} ({{ $class->class_level }})
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <select class="form-select @error('academic_year_id') is-invalid @enderror" 
                                id="academic_year_id" name="academic_year_id" required>
                            <option value="">-- Select Academic Year --</option>
                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->academic_year_id }}" {{ old('academic_year_id', $studentClass->academic_year_id) == $academicYear->academic_year_id ? 'selected' : '' }}>
                                    {{ $academicYear->academic_year_id }} - Semester {{ $academicYear->semester }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" name="status" required>
                        <option value="Active" {{ old('status', $studentClass->status) == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status', $studentClass->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Assignment
                    </button>
                    <a href="{{ route('employee.student_classes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
