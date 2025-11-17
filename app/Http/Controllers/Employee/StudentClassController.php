<?php

namespace App\Http\Controllers\Employee;

use App\Models\MstStudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * StudentClassController
 * 
 * Handles CRUD operations for student class assignments
 * - All READ operations (index, edit) use raw SQL SELECT queries
 * - All WRITE operations (store, update, destroy) use Eloquent Models
 * - Supports multiple student selection via checkboxes
 */
class StudentClassController extends Controller
{
    /**
     * Display list of student class assignments using raw SELECT query
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        // Raw SELECT query to get all student class assignments
        $studentClasses = DB::select('SELECT * FROM mst_student_classes ORDER BY student_class_id DESC');

        return view('employee.student_classes.index', ['studentClasses' => $studentClasses]);
    }

    /**
     * Show form to create new student class assignment
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        // Get list of students using raw SELECT
        $students = DB::select('SELECT student_id, first_name, last_name FROM mst_students WHERE status = ? ORDER BY first_name ASC', ['Active']);

        // Get list of classes using raw SELECT
        $classes = DB::select('SELECT class_id, class_name FROM mst_classes ORDER BY class_name ASC');

        // Get list of academic years using raw SELECT
        $academicYears = DB::select('SELECT academic_year_id, start_date, end_date, semester FROM mst_academic_year WHERE status = ? ORDER BY start_date DESC', ['Active']);

        return view('employee.student_classes.create', [
            'students' => $students,
            'classes' => $classes,
            'academicYears' => $academicYears,
        ]);
    }

    /**
     * Store new student class assignments using Eloquent Model
     * Supports multiple students selection via checkboxes
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        // Validate input
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'required|string|max:50',
            'class_id' => 'required|string|max:50',
            'academic_year_id' => 'required|string|max:50',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        try {
            // Create record for each selected student
            foreach ($validated['student_ids'] as $studentId) {
                // Generate unique ID
                $studentClassId = $studentId . '_' . $validated['class_id'] . '_' . $validated['academic_year_id'];

                MstStudentClass::create([
                    'student_class_id' => $studentClassId,
                    'student_id' => $studentId,
                    'class_id' => $validated['class_id'],
                    'academic_year_id' => $validated['academic_year_id'],
                    'status' => $validated['status'],
                    'created_by' => session('employee_id'),
                    'updated_by' => session('employee_id'),
                ]);
            }

            return redirect()->route('employee.student_classes.index')
                           ->with('success', count($validated['student_ids']) . ' student(s) assigned to class successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to assign students to class: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit student class assignment (fetch data with raw SELECT)
     * 
     * @param string $id Student Class ID
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        // Raw SELECT query to get student class assignment
        $studentClasses = DB::select('SELECT * FROM mst_student_classes WHERE student_class_id = ?', [$id]);

        if (empty($studentClasses)) {
            return redirect()->route('employee.student_classes.index')
                           ->with('error', 'Student Class assignment not found!');
        }

        $studentClass = $studentClasses[0];

        // Get list of classes
        $classes = DB::select('SELECT class_id, class_name FROM mst_classes ORDER BY class_name ASC');

        // Get list of academic years
        $academicYears = DB::select('SELECT academic_year_id, start_date, end_date, semester FROM mst_academic_year WHERE status = ? ORDER BY start_date DESC', ['Active']);

        return view('employee.student_classes.edit', [
            'studentClass' => $studentClass,
            'classes' => $classes,
            'academicYears' => $academicYears,
        ]);
    }

    /**
     * Update student class assignment using Eloquent Model
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $id Student Class ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        // Validate input
        $validated = $request->validate([
            'class_id' => 'required|string|max:50',
            'academic_year_id' => 'required|string|max:50',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        // Add updated_by
        $validated['updated_by'] = session('employee_id');

        try {
            // Find student class assignment using Eloquent
            $studentClass = MstStudentClass::findOrFail($id);

            // Update student class assignment
            $studentClass->update($validated);

            return redirect()->route('employee.student_classes.index')
                           ->with('success', 'Student Class assignment updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to update student class assignment: ' . $e->getMessage());
        }
    }

    /**
     * Delete student class assignment using Eloquent Model
     * 
     * @param string $id Student Class ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        try {
            // Find and delete student class assignment
            $studentClass = MstStudentClass::findOrFail($id);
            $studentClass->delete();

            return redirect()->route('employee.student_classes.index')
                           ->with('success', 'Student Class assignment deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete student class assignment: ' . $e->getMessage());
        }
    }
}
