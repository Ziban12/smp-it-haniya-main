@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Record Attendance by Class</h5>
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

                  <form method="POST" action="{{ route('employee.attendance.store') }}">
    @csrf


                        <div class="mb-3">
                            <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                            <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                                <option value="">-- Select Class --</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->class_id }}" {{ old('class_id') == $class->class_id ? 'selected' : '' }}>
                                        {{ $class->class_name }} (Level {{ $class->class_level }})
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="attendance_date" class="form-label">Attendance Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('attendance_date') is-invalid @enderror" 
                                   id="attendance_date" name="attendance_date" value="{{ old('attendance_date') }}" required>
                            @error('attendance_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Students list will be loaded here via AJAX -->
                        <div id="students-container" class="mb-3" style="display: none;">
                            <label class="form-label">Student Attendance <span class="text-danger">*</span></label>
                            <div id="students-list" class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                <p class="text-muted text-center">Select a class to load students</p>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" id="submit-btn" disabled>
                                <i class="fas fa-save"></i> Save Attendance
                            </button>
                            <a href="{{ route('employee.attendance.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('class_id').addEventListener('change', function() {
        const classId = this.value;
        const container = document.getElementById('students-container');
        const studentsList = document.getElementById('students-list');
        const submitBtn = document.getElementById('submit-btn');

        if (!classId) {
            container.style.display = 'none';
            submitBtn.disabled = true;
            return;
        }

        // Fetch students for selected class
        fetch(`/employee/api/attendance/students/${classId}`)
            .then(response => response.json())
            .then(students => {
                if (students.length === 0) {
                    studentsList.innerHTML = '<p class="text-muted text-center">No students in this class</p>';
                    submitBtn.disabled = true;
                    return;
                }

                let html = '';
                students.forEach(student => {
                    html += `
                        <div class="mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label mb-0">
                                        ${student.first_name} ${student.last_name}
                                        <small class="text-muted">(${student.student_id})</small>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select form-select-sm" name="attendances[${student.student_class_id}][status]" required>
                                        <option value="">Status</option>
                                        <option value="Present">Present</option>
                                        <option value="Absent">Absent</option>
                                        <option value="Late">Late</option>
                                        <option value="Excused">Excused</option>
                                    </select>
                                    <input type="hidden" name="attendances[${student.student_class_id}][student_class_id]" value="${student.student_class_id}">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control form-control-sm" 
                                           name="attendances[${student.student_class_id}][notes]" 
                                           placeholder="Notes (optional)">
                                </div>
                            </div>
                        </div>
                    `;
                });

                studentsList.innerHTML = html;
                container.style.display = 'block';
                submitBtn.disabled = false;
            })
            .catch(error => {
                studentsList.innerHTML = `<p class="text-danger">Error loading students: ${error.message}</p>`;
                submitBtn.disabled = true;
            });
    });
</script>
@endsection
