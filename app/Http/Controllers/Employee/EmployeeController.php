<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MstEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Employee Management Controller
 * 
 * Handles CRUD operations for employee management.
 * Uses Eloquent ORM for all operations.
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
        // Get total count of employees
        $totalEmployees = MstEmployee::where('status', 'Active')->count();

        return view('dashboard.dashboard-employee', [
            'total_employees' => $totalEmployees
        ]);
    }

    /**
     * Display list of employees
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Fetch all active employees ordered by creation date
            $employees = MstEmployee::where('status', 'Active')
                ->orderBy('created_at', 'DESC')
                ->get();

            return view('employees.index', compact('employees'));
        } catch (\Exception $e) {
            Log::error('Error fetching employees: ' . $e->getMessage());
            // Return view with empty collection if error
            return view('employees.index', ['employees' => collect([])]);
        }
    }

    /**
     * Show the form for creating a new employee
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created employee in database
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
{
    try {
        // Validate input
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:mst_employees,username',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'nullable|in:M,F',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'entry_date' => 'nullable|date',
            'level' => 'nullable|string|max:50',
            'status' => 'nullable|in:Active,Inactive',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // ==== Generate Employee ID ====
        $lastEmployee = MstEmployee::orderBy('employee_id', 'DESC')->first();
        if ($lastEmployee) {
            $lastNumber = intval(substr($lastEmployee->employee_id, 3));
            $newId = 'EMP' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newId = 'EMP0001';
        }

        // Handle profile photo upload
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('employees', 'public');
        }

        // Create new employee
        MstEmployee::create([
            'employee_id' => $newId, // ⬅ ID otomatis
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'entry_date' => $validated['entry_date'] ?? date('Y-m-d'),
            'level' => $validated['level'] ?? 'Staff',
            'status' => $validated['status'] ?? 'Active',
            'profile_photo' => $profilePhotoPath,
            'created_by' => session('employee_id') ?? 'SYSTEM',
            'updated_by' => session('employee_id') ?? 'SYSTEM'
        ]);

        Log::info('Employee created successfully: ' . $newId);
        
       return redirect()
    ->route('employee.employees.index')
    ->with('success', 'Employee created successfully!');

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Validation error while creating employee: ' . json_encode($e->errors()));
        return back()->withInput()->withErrors($e->errors());
    } catch (\Exception $e) {
        Log::error('Error creating employee: ' . $e->getMessage());
        return back()->withInput()->with('error', 'Error creating employee: ' . $e->getMessage());
    }
}


    /**
     * Show the form for editing the specified employee
     * 
     * @param string $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            // Fetch employee by ID using Eloquent
            $employee = MstEmployee::findOrFail($id);
            return view('employees.edit', compact('employee'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Employee not found: ' . $id);
            return redirect()->route('employee.employees.index')
                ->with('error', 'Employee not found!');
        } catch (\Exception $e) {
            Log::error('Error fetching employee: ' . $e->getMessage());
            return redirect()->route('employee.employees.index')
                ->with('error', 'Error fetching employee data');
        }
    }

    /**
     * Update the specified employee in database
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
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
                'status' => 'nullable|in:Active,Inactive',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Get employee and update
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
                'status' => $validated['status'] ?? $employee->status,
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ];

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $updateData['profile_photo'] = $request->file('profile_photo')->store('employees', 'public');
            }

            // Only update password if provided
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $employee->update($updateData);

            Log::info('Employee updated successfully: ' . $id);

            return redirect()->route('employee.employees.index')
                ->with('success', 'Employee updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error while updating employee: ' . json_encode($e->errors()));
            return back()->withInput()->withErrors($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Employee not found: ' . $id);
            return redirect()->route('employee.employees.index')
                ->with('error', 'Employee not found!');
        } catch (\Exception $e) {
            Log::error('Error updating employee: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating employee: ' . $e->getMessage());
        }
    }

    /**
     * Delete the specified employee from database
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
{
    try {
        $employee = MstEmployee::findOrFail($id);

        $employee->update([
            'status' => 'Inactive',
            'updated_by' => session('employee_id') ?? 'SYSTEM'
        ]);

        return redirect()
            ->route('employee.employees.index')
            ->with('success', 'Employee deleted successfully!');
    } catch (\Exception $e) {
        return redirect()
            ->route('employee.employees.index')
            ->with('error', 'Error deleting employee: ' . $e->getMessage());
    }
}

}
