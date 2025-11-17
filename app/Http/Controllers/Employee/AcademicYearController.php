<?php

namespace App\Http\Controllers\Employee;

use App\Models\MstAcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AcademicYearController
 * 
 * Handles CRUD operations for academic years
 * - All READ operations (index, edit) use raw SQL SELECT queries
 * - All WRITE operations (store, update, destroy) use Eloquent Models
 */
class AcademicYearController extends Controller
{
    /**
     * Display list of academic years using raw SELECT query
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        // Raw SELECT query to get all academic years
        $academicYears = DB::select('SELECT * FROM mst_academic_year ORDER BY start_date DESC');

        return view('employee.academic_years.index', ['academicYears' => $academicYears]);
    }

    /**
     * Show form to create new academic year
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        return view('employee.academic_years.create');
    }

    /**
     * Store new academic year using Eloquent Model
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
            'academic_year_id' => 'required|string|max:50|unique:mst_academic_year,academic_year_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'semester' => 'required|string|max:10',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        // Add created_by
        $validated['created_by'] = session('employee_id');
        $validated['updated_by'] = session('employee_id');

        // Create academic year using Eloquent
        try {
            MstAcademicYear::create($validated);
            return redirect()->route('employee.academic_years.index')
                           ->with('success', 'Academic Year created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to create academic year: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit academic year (fetch data with raw SELECT)
     * 
     * @param string $id Academic Year ID
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        // Raw SELECT query to get academic year
        $academicYears = DB::select('SELECT * FROM mst_academic_year WHERE academic_year_id = ?', [$id]);

        if (empty($academicYears)) {
            return redirect()->route('employee.academic_years.index')
                           ->with('error', 'Academic Year not found!');
        }

        $academicYear = $academicYears[0];

        return view('employee.academic_years.edit', ['academicYear' => $academicYear]);
    }

    /**
     * Update academic year using Eloquent Model
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $id Academic Year ID
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
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'semester' => 'required|string|max:10',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        // Add updated_by
        $validated['updated_by'] = session('employee_id');

        try {
            // Find academic year using Eloquent
            $academicYear = MstAcademicYear::findOrFail($id);

            // Update academic year
            $academicYear->update($validated);

            return redirect()->route('employee.academic_years.index')
                           ->with('success', 'Academic Year updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to update academic year: ' . $e->getMessage());
        }
    }

    /**
     * Delete academic year using Eloquent Model
     * 
     * @param string $id Academic Year ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Check if user is employee
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }

        try {
            // Find and delete academic year
            $academicYear = MstAcademicYear::findOrFail($id);
            $academicYear->delete();

            return redirect()->route('employee.academic_years.index')
                           ->with('success', 'Academic Year deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete academic year: ' . $e->getMessage());
        }
    }
}
