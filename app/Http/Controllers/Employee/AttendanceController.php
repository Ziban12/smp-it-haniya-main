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
       
    }

    /**
     * Display all attendance records with filtering options
     */
 public function index()
{
    $attendances = DB::select('
        SELECT TOP 1000 
            a.attendance_id,
            a.student_class_id,
            a.attendance_date,
            a.status,
            a.notes,
            a.created_at,
            a.updated_at,
            a.created_by,
            a.updated_by,
            sc.student_id,
            s.first_name,
            s.last_name,
            c.class_name
        FROM txn_attendance a
        JOIN mst_student_classes sc ON a.student_class_id = sc.student_class_id
        JOIN mst_students s ON sc.student_id = s.student_id
        JOIN mst_classes c ON sc.class_id = c.class_id
        ORDER BY a.attendance_date DESC, s.first_name ASC
    ');

    return view('attendance.index', compact('attendances'));
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

        return view('attendance.create', compact('classes'));
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
     */public function store(StoreAttendanceRequest $request)
{
    $validated = $request->validated();

    try {

        $existingAttendances = DB::select(
            'SELECT a.attendance_id, a.student_class_id 
             FROM txn_attendance a
             JOIN mst_student_classes sc ON a.student_class_id = sc.student_class_id
             WHERE a.attendance_date = ? AND sc.class_id = ?',
            [$validated['attendance_date'], $validated['class_id']]
        );

        $existingMap = collect($existingAttendances)->keyBy('student_class_id')->toArray();

        foreach ($validated['attendances'] as $attendance) {

            $studentClassId = $attendance['student_class_id'];

            if (isset($existingMap[$studentClassId])) {

                DB::update(
                    'UPDATE txn_attendance 
                     SET status = ?, notes = ?, updated_by = ?, updated_at = ?
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

                // === Generate attendance_id otomatis: ATD0001 ===
                $lastAttendance = TxnAttendance::orderBy('attendance_id', 'DESC')->first();

                if ($lastAttendance) {
                    $lastNumber = intval(substr($lastAttendance->attendance_id, 3));
                    $newId = 'ATD' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newId = 'ATD0001';
                }

                // === Insert data baru ===
                TxnAttendance::create([
                    'attendance_id'      => $newId,
                    'student_class_id'   => $studentClassId,
                    'attendance_date'    => $validated['attendance_date'],
                    'status'             => $attendance['status'],
                    'notes'              => $attendance['notes'] ?? null,
                    'created_by'         => session('employee_id'),
                    'created_at'         => now(),
                    'updated_by'         => session('employee_id'),
                    'updated_at'         => now(),
                ]);
            }
        }

        return redirect()
            ->route('employee.attendance.index')
            ->with('success', count($validated['attendances']) . ' attendance record(s) saved successfully!');

    } catch (\Exception $e) {

        return back()
            ->withInput()
            ->with('error', 'Failed to save attendance: ' . $e->getMessage());
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
