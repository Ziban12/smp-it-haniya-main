@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Create New Schedule</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Validation Errors</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('employee.schedules.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                            <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                                <option value="">-- Select Class --</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->class_id }}" {{ old('class_id') == $class->class_id ? 'selected' : '' }}>
                                        {{ $class->class_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                            <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                                <option value="">-- Select Subject --</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->subject_id }}" {{ old('subject_id') == $subject->subject_id ? 'selected' : '' }}>
                                        {{ $subject->subject_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="teacher_id" class="form-label">Teacher <span class="text-danger">*</span></label>
                            <select class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
                                <option value="">-- Select Teacher --</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->teacher_id }}" {{ old('teacher_id') == $teacher->teacher_id ? 'selected' : '' }}>
                                        {{ $teacher->first_name }} {{ $teacher->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select class="form-select @error('academic_year_id') is-invalid @enderror" id="academic_year_id" name="academic_year_id" required>
                                <option value="">-- Select Academic Year --</option>
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->academic_year_id }}" {{ old('academic_year_id') == $year->academic_year_id ? 'selected' : '' }}>
                                        {{ $year->academic_year_id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="day" class="form-label">Day <span class="text-danger">*</span></label>
                            <select class="form-select @error('day') is-invalid @enderror" id="day" name="day" required>
                                <option value="">-- Select Day --</option>
                                @foreach ($days as $day)
                                    <option value="{{ $day }}" {{ old('day') == $day ? 'selected' : '' }}>{{ $day }}</option>
                                @endforeach
                            </select>
                            @error('day')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                       id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                       id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Create Schedule
                            </button>
                            <a href="{{ route('employee.schedules.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
