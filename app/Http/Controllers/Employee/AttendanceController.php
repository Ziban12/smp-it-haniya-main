<?php

namespace App\Http\Controllers\Employee;

use App\Models\TxnAttendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreAttendanceRequest;

class AttendanceController extends Controller
{
    // Check authentication before accessing
    public function __construct()
    {
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }
    }

    /**
     * Display all attendance records with filtering options
     */
    public function index()
    {
        $attendances = DB::select('
            SELECT a.*, sc.student_id, s.first_name, s.last_name, c.class_name
            FROM txn_attendance a
            JOIN mst_student_classes sc ON a.student_class_id = sc.student_class_id
            JOIN mst_students s ON sc.student_id = s.student_id
            JOIN mst_classes c ON sc.class_id = c.class_id
            ORDER BY a.attendance_date DESC, s.first_name ASC
            LIMIT 1000
        ');

        return view('employee.attendance.index', compact('attendances'));
    }

    /**
     * Show form for bulk attendance input by class
     * Gets list of classes for selection
     */
    public function create()
    {
        $classes = DB::select('
            SELECT DISTINCT c.class_id, c.class_name, c.class_level
            FROM mst_classes c
            ORDER BY c.class_name ASC
        ');

        return view('employee.attendance.create', compact('classes'));
    }

    /**
     * AJAX endpoint: Get students for selected class
     * Used for dynamic filtering
     */
    public function getStudentsByClass($classId)
    {
        $students = DB::select('
            SELECT sc.student_class_id, s.student_id, s.first_name, s.last_name
            FROM mst_student_classes sc
            JOIN mst_students s ON sc.student_id = s.student_id
            WHERE sc.class_id = ? AND sc.status = ?
            ORDER BY s.first_name ASC
        ', [$classId, 'Active']);

        return response()->json($students);
    }

    /**
     * Store bulk attendance records
     * Handles multiple student attendance entries at once
     */
    public function store(StoreAttendanceRequest $request)
    {
        $validated = $request->validated();

        try {
            // Get existing attendance records for this date and class
            $existingAttendances = DB::select(
                'SELECT a.attendance_id, a.student_class_id 
                 FROM txn_attendance a
                 JOIN mst_student_classes sc ON a.student_class_id = sc.student_class_id
                 WHERE a.attendance_date = ? AND sc.class_id = ?',
                [$validated['attendance_date'], $validated['class_id']]
            );

            $existingMap = collect($existingAttendances)->keyBy('student_class_id')->toArray();

            // Process each attendance entry
            foreach ($validated['attendances'] as $attendance) {
                $studentClassId = $attendance['student_class_id'];
                $attendanceData = [
                    'student_class_id' => $studentClassId,
                    'attendance_date' => $validated['attendance_date'],
                    'status' => $attendance['status'],
                    'notes' => $attendance['notes'] ?? null,
                    'updated_by' => session('employee_id'),
                    'updated_at' => now(),
                ];

                if (isset($existingMap[$studentClassId])) {
                    // Update existing attendance
                    DB::update(
                        'UPDATE txn_attendance SET status = ?, notes = ?, updated_by = ?, updated_at = ? 
                         WHERE attendance_id = ?',
                        [
                            $attendance['status'],
                            $attendance['notes'] ?? null,
                            session('employee_id'),
                            now(),
                            $existingMap[$studentClassId]['attendance_id']
                        ]
                    );
                } else {
                    // Create new attendance
                    $attendanceId = $studentClassId . '_' . $validated['attendance_date'];
                    $attendanceData['attendance_id'] = $attendanceId;
                    $attendanceData['created_by'] = session('employee_id');
                    $attendanceData['created_at'] = now();

                    TxnAttendance::create($attendanceData);
                }
            }

            return redirect()->route('employee.attendance.index')
                           ->with('success', 'Attendance records saved successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error saving attendance: ' . $e->getMessage());
        }
    }

    /**
     * Show attendance records for a specific class and date
     */
    public function show($classId, $date)
    {
        $class = DB::select(
            'SELECT * FROM mst_classes WHERE class_id = ?',
            [$classId]
        );

        if (empty($class)) {
            return redirect()->route('employee.attendance.index')
                           ->with('error', 'Class not found!');
        }

        $students = DB::select('
            SELECT sc.student_class_id, s.student_id, s.first_name, s.last_name,
                   a.attendance_id, a.status, a.notes
            FROM mst_student_classes sc
            JOIN mst_students s ON sc.student_id = s.student_id
            LEFT JOIN txn_attendance a ON a.student_class_id = sc.student_class_id 
                                         AND DATE(a.attendance_date) = ?
            WHERE sc.class_id = ? AND sc.status = ?
            ORDER BY s.first_name ASC
        ', [$date, $classId, 'Active']);

        return view('employee.attendance.edit', [
            'class' => $class[0],
            'date' => $date,
            'students' => $students
        ]);
    }

    /**
     * Delete attendance record
     */
    public function destroy($id)
    {
        $attendance = TxnAttendance::findOrFail($id);
        $attendance->delete();

        return redirect()->route('employee.attendance.index')
                       ->with('success', 'Attendance record deleted successfully!');
    }
}
