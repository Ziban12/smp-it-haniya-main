<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MstTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Teacher Management Controller
 * 
 * Handles CRUD operations for teacher management.
 * Uses raw SELECT for reading, Eloquent for create/update/delete.
 */
class TeacherController extends Controller
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
     * Display list of teachers
     * 
     * RAW SELECT query to fetch all teachers
     * 
     * @return \Illuminate\View\View
     */
   public function index()
{
    // RAW SELECT QUERY (hasil array) → ubah ke Collection
    $teachers = collect(DB::select(
        'SELECT * FROM mst_teachers WHERE status = ? ORDER BY teacher_id DESC',
        ['Active']
    ));

    return view('teachers.index', compact('teachers'));
}


    /**
     * Show the form for creating a new teacher
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('teachers.create');
    }

    /**
     * Store a newly created teacher in database
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
            'teacher_id' => 'required|string|unique:mst_teachers',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'npk' => 'required|string|max:50|unique:mst_teachers',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'nullable|in:M,F',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'entry_date' => 'nullable|date',
            'level' => 'nullable|string|max:50',
        ]);

        // ELOQUENT CREATE: Create new teacher
        MstTeacher::create([
            'teacher_id' => $validated['teacher_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'npk' => $validated['npk'],
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'entry_date' => $validated['entry_date'] ?? now(),
            'level' => $validated['level'] ?? 'Teacher',
            'status' => 'Active',
            'created_by' => session('employee_id')
        ]);

        return redirect()->route('employee.teachers.index')
            ->with('success', 'Teacher created successfully!');
    }

    /**
     * Show the form for editing the specified teacher
     * 
     * RAW SELECT to fetch teacher data
     * 
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // RAW SELECT QUERY: Fetch teacher by ID
        $teachers = DB::select(
            'SELECT * FROM mst_teachers WHERE teacher_id = ?',
            [$id]
        );

        if (empty($teachers)) {
            return redirect()->route('employee.teachers.index')
                ->with('error', 'Teacher not found!');
        }

        $teacher = $teachers[0];

        return view('employee.teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified teacher in database
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
            'npk' => 'required|string|max:50|unique:mst_teachers,npk,' . $id . ',teacher_id',
            'password' => 'nullable|string|min:6|confirmed',
            'gender' => 'nullable|in:M,F',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'entry_date' => 'nullable|date',
            'level' => 'nullable|string|max:50',
        ]);

        // ELOQUENT UPDATE: Update teacher record
        $teacher = MstTeacher::findOrFail($id);
        
        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'npk' => $validated['npk'],
            'gender' => $validated['gender'] ?? $teacher->gender,
            'birth_place' => $validated['birth_place'] ?? $teacher->birth_place,
            'birth_date' => $validated['birth_date'] ?? $teacher->birth_date,
            'address' => $validated['address'] ?? $teacher->address,
            'phone' => $validated['phone'] ?? $teacher->phone,
            'entry_date' => $validated['entry_date'] ?? $teacher->entry_date,
            'level' => $validated['level'] ?? $teacher->level,
            'updated_by' => session('employee_id')
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $teacher->update($updateData);

        return redirect()->route('employee.teachers.index')
            ->with('success', 'Teacher updated successfully!');
    }

    /**
     * Delete the specified teacher from database
     * 
     * Uses Eloquent Model for DELETE operation
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // ELOQUENT DELETE: Mark teacher as inactive
        $teacher = MstTeacher::findOrFail($id);
        
        $teacher->update([
            'status' => 'Inactive',
            'updated_by' => session('employee_id')
        ]);

        return redirect()->route('employee.teachers.index')
            ->with('success', 'Teacher deleted successfully!');
    }
}
