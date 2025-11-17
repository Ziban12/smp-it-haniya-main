<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MstStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Student Login Controller
 * 
 * Handles student authentication.
 * Uses raw SELECT for authentication, session-based login.
 */
class StudentLoginController extends Controller
{
    /**
     * Show the student login form
     * 
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.student-login');
    }

    /**
     * Handle student login authentication
     * 
     * Raw SELECT query to authenticate student by NIS (student ID).
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'nis' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        // RAW SELECT QUERY: Fetch student by NIS
        $student = DB::select(
            'SELECT * FROM mst_students WHERE nis = ? AND status = ?',
            [$validated['nis'], 'Active']
        );

        // Verify student exists and password matches
        if (!empty($student) && Hash::check($validated['password'], $student[0]->password)) {
            // Store in session
            session([
                'student_id' => $student[0]->student_id,
                'name' => $student[0]->first_name . ' ' . $student[0]->last_name,
                'nis' => $student[0]->nis,
                'user_type' => 'student'
            ]);

            return redirect()->route('student.dashboard')
                ->with('success', 'Welcome, ' . session('name') . '!');
        }

        return back()
            ->withErrors(['nis' => 'Invalid credentials or account is inactive.'])
            ->onlyInput('nis');
    }

    /**
     * Logout the student
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        session()->flush();
        return redirect()->route('student.login')
            ->with('success', 'Logged out successfully!');
    }
}
