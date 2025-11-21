<?php

namespace App\Http\Controllers\Employee;

use App\Models\MstStudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Check if user is employee
        

        // Raw SELECT query to get all student class assignments
        $studentClasses = DB::select('SELECT * FROM mst_student_classes ORDER BY student_class_id DESC');

        return view('student_classes.index', ['studentClasses' => $studentClasses]);
    }

    /**
     * Show form to create new student class assignment
     * 
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
{
    $students = DB::table('mst_students')
        ->select('student_id', 'first_name', 'last_name')
        ->where('status', 'Active')
        ->orderBy('first_name', 'asc')
        ->get();

    $classes = DB::table('mst_classes')
        ->select('class_id', 'class_name', 'class_level')
        ->orderBy('class_name', 'asc')
        ->get();

    $academicYears = DB::table('mst_academic_year')
        ->select('academic_year_id', 'semester')
        ->where('status', 'Active')
        ->orderBy('start_date', 'desc')
        ->get();

    return view('student_classes.create', compact('students', 'classes', 'academicYears'));
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
    $validated = $request->validate([
        'student_ids' => 'required|array|min:1',
        'student_ids.*' => 'required|string|max:50',
        'class_id' => 'required|string|max:50',
        'academic_year_id' => 'required|string|max:50',
        'status' => 'required|string|in:Active,Inactive',
    ]);

    try {
        foreach ($validated['student_ids'] as $studentId) {

            // === Generate student_class_id otomatis ===
            $lastStudentClass = MstStudentClass::orderBy('student_class_id', 'DESC')->first();
            if ($lastStudentClass) {
                // Ambil angka terakhir, misal SC0004 → 4
                $lastNumber = intval(substr($lastStudentClass->student_class_id, 2));
                $newId = 'STC' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newId = 'STC0001';
            }

            MstStudentClass::create([
                'student_class_id' => $newId,
                'student_id' => $studentId,
                'class_id' => $validated['class_id'],
                'academic_year_id' => $validated['academic_year_id'],
                'status' => $validated['status'],
                'created_by' => session('employee_id') ?? 'SYSTEM',
                'updated_by' => session('employee_id') ?? 'SYSTEM',
            ]);
        }

        return redirect()->route('employee.student-classes.index')
                         ->with('success', count($validated['student_ids']) . ' student(s) assigned successfully!');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Failed to assign students: ' . $e->getMessage());
    }
}


    /**
     * Show form to edit student class assignment (fetch data with raw SELECT)
     * 
     * @param string $id Student Class ID
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
  public function edit($id)
{
    // Cek user employee
  
    // Ambil student class assignment
    $studentClass = DB::table('mst_student_classes')
        ->where('student_class_id', $id)
        ->first();

    if (!$studentClass) {
        return redirect()->route('student-classes.index')
                         ->with('error', 'Student Class assignment not found!');
    }

    // Ambil list classes
    $classes = DB::table('mst_classes')
        ->orderBy('class_name', 'asc')
        ->get();

    // Ambil list academic years
    $academicYears = DB::table('mst_academic_year')
        ->where('status', 'Active')
        ->orderBy('start_date', 'desc')
        ->get();

    return view('student_classes.edit', compact('studentClass', 'classes', 'academicYears'));
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
    $validated = $request->validate([
        'class_id' => 'required|string|max:50',
        'academic_year_id' => 'required|string|max:50',
        'status' => 'required|string|in:Active,Inactive',
    ]);

    $validated['updated_by'] = session('employee_id');

    $studentClass = MstStudentClass::findOrFail($id);
    $studentClass->update($validated);

    return redirect()->route('employee.student-classes.index')
                     ->with('success', 'Student Class assignment updated successfully!');
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
