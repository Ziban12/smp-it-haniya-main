<?php

namespace App\Http\Controllers\Employee;

use App\Models\MstClass;
use App\Models\MstTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ClassController
 * 
 * Handles CRUD operations for classes
 * - All READ operations (index, edit) use raw SQL SELECT queries
 * - All WRITE operations (store, update, destroy) use Eloquent Models
 */
class ClassController extends Controller
{
    /**
     * Display list of classes using raw SELECT query
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        // Raw SELECT query to get all classes
        $classes = DB::select('SELECT * FROM mst_classes ORDER BY class_id DESC');

        return view('employee.classes.index', ['classes' => $classes]);
    }

    /**
     * Show form to create new class
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        // Get list of teachers using raw SELECT
        $teachers = DB::select('SELECT teacher_id, first_name, last_name FROM mst_teachers WHERE status = ? ORDER BY first_name ASC', ['Active']);

        return view('employee.classes.create', ['teachers' => $teachers]);
    }

    /**
     * Store new class using Eloquent Model
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
            'class_id' => 'required|string|max:50|unique:mst_classes,class_id',
            'class_name' => 'required|string|max:100',
            'class_level' => 'required|string|max:10',
            'homeroom_teacher_id' => 'required|string|max:50',
        ]);

        // Add created_by and updated_by
        $validated['created_by'] = session('employee_id');
        $validated['updated_by'] = session('employee_id');

        // Create class using Eloquent
        try {
            MstClass::create($validated);
            return redirect()->route('employee.classes.index')
                           ->with('success', 'Class created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to create class: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit class (fetch data with raw SELECT)
     * 
     * @param string $id Class ID
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        // Raw SELECT query to get class
        $classes = DB::select('SELECT * FROM mst_classes WHERE class_id = ?', [$id]);

        if (empty($classes)) {
            return redirect()->route('employee.classes.index')
                           ->with('error', 'Class not found!');
        }

        $class = $classes[0];

        // Get list of teachers using raw SELECT
        $teachers = DB::select('SELECT teacher_id, first_name, last_name FROM mst_teachers WHERE status = ? ORDER BY first_name ASC', ['Active']);

        return view('employee.classes.edit', ['class' => $class, 'teachers' => $teachers]);
    }

    /**
     * Update class using Eloquent Model
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $id Class ID
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
            'class_name' => 'required|string|max:100',
            'class_level' => 'required|string|max:10',
            'homeroom_teacher_id' => 'required|string|max:50',
        ]);

        // Add updated_by
        $validated['updated_by'] = session('employee_id');

        try {
            // Find class using Eloquent
            $class = MstClass::findOrFail($id);

            // Update class
            $class->update($validated);

            return redirect()->route('employee.classes.index')
                           ->with('success', 'Class updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to update class: ' . $e->getMessage());
        }
    }

    /**
     * Delete class using Eloquent Model
     * 
     * @param string $id Class ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        try {
            // Find and delete class
            $class = MstClass::findOrFail($id);
            $class->delete();

            return redirect()->route('employee.classes.index')
                           ->with('success', 'Class deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete class: ' . $e->getMessage());
        }
    }
}
