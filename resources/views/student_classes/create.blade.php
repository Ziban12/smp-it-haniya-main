@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-plus-circle"></i> Assign Students to Class</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('employee.student-classes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Assignments
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('employee.student-classes.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                        <select class="form-select @error('class_id') is-invalid @enderror" 
                                id="class_id" name="class_id" required>
                            <option value="">-- Select Class --</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->class_id }}" {{ old('class_id') == $class->class_id ? 'selected' : '' }}>
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
                                <option value="{{ $academicYear->academic_year_id }}" {{ old('academic_year_id') == $academicYear->academic_year_id ? 'selected' : '' }}>
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
                        <option value="">-- Select Status --</option>
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Students <span class="text-danger">*</span></label>
                    <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                        @forelse ($students as $student)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="student_ids[]" 
                                       value="{{ $student->student_id }}" 
                                       id="student_{{ $student->student_id }}"
                                       {{ in_array($student->student_id, old('student_ids', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="student_{{ $student->student_id }}">
                                    {{ $student->first_name }} {{ $student->last_name }} 
                                    <small class="text-muted">({{ $student->student_id }})</small>
                                </label>
                            </div>
                        @empty
                            <p class="text-muted">No active students available</p>
                        @endforelse
                    </div>
                    @error('student_ids')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Assign Students
                    </button>
                    <a href="{{ route('employee.student-classes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Add select/deselect all functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Check if there are checkboxes
        const checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
        
        // Add a helper to count selected
        const updateSelectionCount = () => {
            const selected = document.querySelectorAll('input[name="student_ids[]"]:checked').length;
            console.log(selected + ' students selected');
        };
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectionCount);
        });
        
        updateSelectionCount();
    });
</script>
@endsection
