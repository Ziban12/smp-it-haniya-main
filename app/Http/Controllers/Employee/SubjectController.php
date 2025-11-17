<?php

namespace App\Http\Controllers\Employee;

use App\Models\MstSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * SubjectController
 * 
 * Handles CRUD operations for subjects
 * - All READ operations (index, edit) use raw SQL SELECT queries
 * - All WRITE operations (store, update, destroy) use Eloquent Models
 */
class SubjectController extends Controller
{
    /**
     * Display list of subjects using raw SELECT query
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        // Raw SELECT query to get all subjects
        $subjects = DB::select('SELECT * FROM mst_subjects ORDER BY subject_id DESC');

        return view('employee.subjects.index', ['subjects' => $subjects]);
    }

    /**
     * Show form to create new subject
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        return view('employee.subjects.create');
    }

    /**
     * Store new subject using Eloquent Model
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
            'subject_id' => 'required|string|max:50|unique:mst_subjects,subject_id',
            'subject_name' => 'required|string|max:100',
            'subject_code' => 'required|string|max:20',
            'class_level' => 'required|string|max:10',
            'description' => 'nullable|string|max:500',
        ]);

        // Add created_by to data
        $validated['created_by'] = session('employee_id');
        $validated['updated_by'] = session('employee_id');

        // Create subject using Eloquent
        try {
            MstSubject::create($validated);
            return redirect()->route('employee.subjects.index')
                           ->with('success', 'Subject created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to create subject: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit subject (fetch data with raw SELECT)
     * 
     * @param string $id Subject ID
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        // Raw SELECT query to get subject
        $subjects = DB::select('SELECT * FROM mst_subjects WHERE subject_id = ?', [$id]);

        if (empty($subjects)) {
            return redirect()->route('employee.subjects.index')
                           ->with('error', 'Subject not found!');
        }

        $subject = $subjects[0];

        return view('employee.subjects.edit', ['subject' => $subject]);
    }

    /**
     * Update subject using Eloquent Model
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $id Subject ID
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
            'subject_name' => 'required|string|max:100',
            'subject_code' => 'required|string|max:20',
            'class_level' => 'required|string|max:10',
            'description' => 'nullable|string|max:500',
        ]);

        // Add updated_by
        $validated['updated_by'] = session('employee_id');

        try {
            // Find subject using Eloquent
            $subject = MstSubject::findOrFail($id);

            // Update subject
            $subject->update($validated);

            return redirect()->route('employee.subjects.index')
                           ->with('success', 'Subject updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to update subject: ' . $e->getMessage());
        }
    }

    /**
     * Delete subject using Eloquent Model
     * 
     * @param string $id Subject ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        try {
            // Find and delete subject
            $subject = MstSubject::findOrFail($id);
            $subject->delete();

            return redirect()->route('employee.subjects.index')
                           ->with('success', 'Subject deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete subject: ' . $e->getMessage());
        }
    }
}
