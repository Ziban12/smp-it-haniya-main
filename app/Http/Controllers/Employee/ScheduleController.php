<?php

namespace App\Http\Controllers\Employee;

use App\Models\TxnSchedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;

class ScheduleController extends Controller
{
    // Check authentication before accessing
    public function __construct()
    {
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }
    }

    /**
     * Display all schedules with filtering
     */
    public function index()
    {
        $schedules = DB::select('
            SELECT s.*, c.class_name, sub.subject_name, t.first_name as teacher_name, ay.academic_year_id
            FROM txn_schedules s
            JOIN mst_classes c ON s.class_id = c.class_id
            JOIN mst_subjects sub ON s.subject_id = sub.subject_id
            JOIN mst_teachers t ON s.teacher_id = t.teacher_id
            JOIN mst_academic_year ay ON s.academic_year_id = ay.academic_year_id
            ORDER BY s.day ASC, s.start_time ASC
            LIMIT 1000
        ');

        return view('employee.schedules.index', compact('schedules'));
    }

    /**
     * Show form for creating new schedule
     */
    public function create()
    {
        $classes = DB::select('SELECT class_id, class_name FROM mst_classes ORDER BY class_name ASC');
        $subjects = DB::select('SELECT subject_id, subject_name FROM mst_subjects ORDER BY subject_name ASC');
        $teachers = DB::select('SELECT teacher_id, first_name, last_name FROM mst_teachers WHERE status = ? ORDER BY first_name ASC', ['Active']);
        $academicYears = DB::select('SELECT academic_year_id FROM mst_academic_year WHERE status = ? ORDER BY start_date DESC', ['Active']);

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return view('employee.schedules.create', compact('classes', 'subjects', 'teachers', 'academicYears', 'days'));
    }

    /**
     * Store new schedule
     */
    public function store(StoreScheduleRequest $request)
    {
        $validated = $request->validated();

        try {
            $scheduleId = $validated['class_id'] . '_' . $validated['subject_id'] . '_' . $validated['day'];

            TxnSchedule::create([
                'schedule_id' => $scheduleId,
                'class_id' => $validated['class_id'],
                'subject_id' => $validated['subject_id'],
                'teacher_id' => $validated['teacher_id'],
                'academic_year_id' => $validated['academic_year_id'],
                'day' => $validated['day'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'created_by' => session('employee_id'),
                'updated_by' => session('employee_id'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('employee.schedules.index')
                           ->with('success', 'Schedule created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error creating schedule: ' . $e->getMessage());
        }
    }

    /**
     * Show form for editing schedule
     */
    public function edit($id)
    {
        $schedule = DB::select(
            'SELECT * FROM txn_schedules WHERE schedule_id = ?',
            [$id]
        );

        if (empty($schedule)) {
            return redirect()->route('employee.schedules.index')
                           ->with('error', 'Schedule not found!');
        }

        $classes = DB::select('SELECT class_id, class_name FROM mst_classes ORDER BY class_name ASC');
        $subjects = DB::select('SELECT subject_id, subject_name FROM mst_subjects ORDER BY subject_name ASC');
        $teachers = DB::select('SELECT teacher_id, first_name, last_name FROM mst_teachers WHERE status = ? ORDER BY first_name ASC', ['Active']);
        $academicYears = DB::select('SELECT academic_year_id FROM mst_academic_year WHERE status = ? ORDER BY start_date DESC', ['Active']);

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('employee.schedules.edit', [
            'schedule' => $schedule[0],
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'academicYears' => $academicYears,
            'days' => $days
        ]);
    }

    /**
     * Update schedule
     */
    public function update(UpdateScheduleRequest $request, $id)
    {
        $schedule = TxnSchedule::findOrFail($id);

        $validated = $request->validated();

        $validated['updated_by'] = session('employee_id');
        $validated['updated_at'] = now();

        $schedule->update($validated);

        return redirect()->route('employee.schedules.index')
                       ->with('success', 'Schedule updated successfully!');
    }

    /**
     * Delete schedule
     */
    public function destroy($id)
    {
        $schedule = TxnSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('employee.schedules.index')
                       ->with('success', 'Schedule deleted successfully!');
    }
}
