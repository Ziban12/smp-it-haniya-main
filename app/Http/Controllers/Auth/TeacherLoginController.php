<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MstTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Teacher Login Controller
 * 
 * Handles teacher authentication.
 * Uses raw SELECT for authentication, session-based login.
 */
class TeacherLoginController extends Controller
{
    /**
     * Show the teacher login form
     * 
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.teacher-login');
    }

    /**
     * Handle teacher login authentication
     * 
     * Raw SELECT query to authenticate teacher by NPK (teacher ID).
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'npk' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        // RAW SELECT QUERY: Fetch teacher by NPK
        $teacher = DB::select(
            'SELECT * FROM mst_teachers WHERE npk = ? AND status = ?',
            [$validated['npk'], 'Active']
        );

        // Verify teacher exists and password matches
        if (!empty($teacher) && Hash::check($validated['password'], $teacher[0]->password)) {
            // Store in session
            session([
                'teacher_id' => $teacher[0]->teacher_id,
                'name' => $teacher[0]->first_name . ' ' . $teacher[0]->last_name,
                'level' => $teacher[0]->level,
                'user_type' => 'teacher'
            ]);

            return redirect()->route('teacher.dashboard')
                ->with('success', 'Welcome, ' . session('name') . '!');
        }

        return back()
            ->withErrors(['npk' => 'Invalid credentials or account is inactive.'])
            ->onlyInput('npk');
    }

    /**
     * Logout the teacher
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        session()->flush();
        return redirect()->route('teacher.login')
            ->with('success', 'Logged out successfully!');
    }
}
