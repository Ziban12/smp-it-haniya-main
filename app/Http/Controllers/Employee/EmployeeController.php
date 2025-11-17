<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MstEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Employee Management Controller
 * 
 * Handles CRUD operations for employee management.
 * Uses raw SELECT for reading, Eloquent for create/update/delete.
 */
class EmployeeController extends Controller
{
    /**
     * Middleware to ensure employee is logged in
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!session('user_type') || session('user_type') !== 'employee') {
                return redirect()->route('employee.login');
            }
            return $next($request);
        });
    }

    /**
     * Display dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // RAW SELECT: Get total count of employees
        $totalEmployees = DB::select('SELECT COUNT(*) as count FROM mst_employees WHERE status = ?', ['Active']);

        return view('dashboard.dashboard-employee', [
            'total_employees' => $totalEmployees[0]->count ?? 0
        ]);
    }

    /**
     * Display list of employees
     * 
     * RAW SELECT query to fetch all employees
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // RAW SELECT QUERY: Fetch all employees
        $employees = DB::select(
            'SELECT * FROM mst_employees WHERE status = ? ORDER BY employee_id DESC',
            ['Active']
        );

        return view('employee.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('employee.employees.create');
    }

    /**
     * Store a newly created employee in database
     * 
     * Uses Eloquent Model for CREATE operation
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'employee_id' => 'required|string|unique:mst_employees',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:mst_employees',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'nullable|in:M,F',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'entry_date' => 'nullable|date',
            'level' => 'nullable|string|max:50',
        ]);

        // ELOQUENT CREATE: Create new employee
        MstEmployee::create([
            'employee_id' => $validated['employee_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'entry_date' => $validated['entry_date'] ?? now(),
            'level' => $validated['level'] ?? 'Staff',
            'status' => 'Active',
            'created_by' => session('employee_id')
        ]);

        return redirect()->route('employee.employees.index')
            ->with('success', 'Employee created successfully!');
    }

    /**
     * Show the form for editing the specified employee
     * 
     * RAW SELECT to fetch employee data
     * 
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // RAW SELECT QUERY: Fetch employee by ID
        $employees = DB::select(
            'SELECT * FROM mst_employees WHERE employee_id = ?',
            [$id]
        );

        if (empty($employees)) {
            return redirect()->route('employee.employees.index')
                ->with('error', 'Employee not found!');
        }

        $employee = $employees[0];

        return view('employee.employees.edit', compact('employee'));
    }

    /**
     * Update the specified employee in database
     * 
     * Uses Eloquent Model for UPDATE operation
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validate input
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:mst_employees,username,' . $id . ',employee_id',
            'password' => 'nullable|string|min:6|confirmed',
            'gender' => 'nullable|in:M,F',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'entry_date' => 'nullable|date',
            'level' => 'nullable|string|max:50',
        ]);

        // ELOQUENT UPDATE: Update employee record
        $employee = MstEmployee::findOrFail($id);
        
        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'gender' => $validated['gender'] ?? $employee->gender,
            'birth_place' => $validated['birth_place'] ?? $employee->birth_place,
            'birth_date' => $validated['birth_date'] ?? $employee->birth_date,
            'address' => $validated['address'] ?? $employee->address,
            'phone' => $validated['phone'] ?? $employee->phone,
            'entry_date' => $validated['entry_date'] ?? $employee->entry_date,
            'level' => $validated['level'] ?? $employee->level,
            'updated_by' => session('employee_id')
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $employee->update($updateData);

        return redirect()->route('employee.employees.index')
            ->with('success', 'Employee updated successfully!');
    }

    /**
     * Delete the specified employee from database
     * 
     * Uses Eloquent Model for DELETE operation
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // ELOQUENT DELETE: Mark employee as inactive
        $employee = MstEmployee::findOrFail($id);
        
        $employee->update([
            'status' => 'Inactive',
            'updated_by' => session('employee_id')
        ]);

        return redirect()->route('employee.employees.index')
            ->with('success', 'Employee deleted successfully!');
    }
}
