<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return session('user_type') === 'Employee';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'class_id' => [
                'required',
                'string',
                'exists:mst_classes,class_id',
            ],
            'attendance_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],
            'attendances' => [
                'required',
                'array',
                'min:1',
            ],
            'attendances.*.student_class_id' => [
                'required',
                'string',
                'exists:mst_student_classes,student_class_id',
            ],
            'attendances.*.status' => [
                'required',
                'in:Present,Absent,Late,Excused',
            ],
            'attendances.*.notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'class_id.required' => 'Class is required.',
            'class_id.exists' => 'Selected class does not exist.',
            'attendance_date.required' => 'Attendance date is required.',
            'attendance_date.date' => 'Attendance date must be a valid date.',
            'attendance_date.before_or_equal' => 'Attendance date cannot be in the future.',
            'attendances.required' => 'At least one student attendance record is required.',
            'attendances.min' => 'At least one student attendance record is required.',
            'attendances.*.student_class_id.required' => 'Student is required for each record.',
            'attendances.*.student_class_id.exists' => 'Selected student does not exist.',
            'attendances.*.status.required' => 'Attendance status is required for each student.',
            'attendances.*.status.in' => 'Invalid attendance status. Must be Present, Absent, Late, or Excused.',
            'attendances.*.notes.max' => 'Notes cannot exceed 500 characters.',
        ];
    }
}
