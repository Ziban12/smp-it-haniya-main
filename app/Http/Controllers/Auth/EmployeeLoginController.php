<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MstEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Employee Login Controller
 * 
 * Handles employee (admin/staff) authentication.
 * Uses raw SELECT for authentication, session-based login.
 */
class EmployeeLoginController extends Controller
{
    /**
     * Show the employee login form
     * 
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.employee-login');
    }

    /**
     * Handle employee login authentication
     * 
     * Raw SELECT query to authenticate employee by username.
     * Uses Eloquent for model operations.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        // RAW SELECT QUERY: Fetch employee by username
        $employee = DB::select(
            'SELECT * FROM mst_employees WHERE username = ? AND status = ?',
            [$validated['username'], 'Active']
        );

        // Verify employee exists and password matches
        if (!empty($employee) && Hash::check($validated['password'], $employee[0]->password)) {
            // Store in session
            session([
                'employee_id' => $employee[0]->employee_id,
                'name' => $employee[0]->first_name . ' ' . $employee[0]->last_name,
                'level' => $employee[0]->level,
                'user_type' => 'employee'
            ]);

            return redirect()->route('employee.dashboard')
                ->with('success', 'Welcome, ' . session('name') . '!');
        }

        return back()
            ->withErrors(['username' => 'Invalid credentials or account is inactive.'])
            ->onlyInput('username');
    }

    /**
     * Logout the employee
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        session()->flush();
        return redirect()->route('employee.login')
            ->with('success', 'Logged out successfully!');
    }
}
