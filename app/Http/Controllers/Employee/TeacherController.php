<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MstTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Teacher Management Controller
 * 
 * Handles CRUD operations for teacher management.
 * Attributes: teacher_id, first_name, last_name, npk, gender, birth_place,
 *             birth_date, profile_photo, address, phone, entry_date, password,
 *             level, status, created_at, updated_at, created_by, updated_by
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
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Fetch all active teachers ordered by creation date
            $teachers = MstTeacher::where('status', 'Active')
                ->orderBy('created_at', 'DESC')
                ->get();

            return view('teachers.index', compact('teachers'));
        } catch (\Exception $e) {
            Log::error('Error fetching teachers: ' . $e->getMessage());
            return view('teachers.index', ['teachers' => collect([])]);
        }
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
            'npk' => 'required|string|max:50|unique:mst_teachers,npk',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'nullable|in:M,F',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'entry_date' => 'nullable|date',
            'level' => 'nullable|string|max:50',
            'status' => 'nullable|in:Active,Inactive'
        ]);

        // Generate ID terbaru
        $lastTeacher = MstTeacher::orderBy('teacher_id', 'DESC')->first();
        if ($lastTeacher) {
            $lastNumber = intval(substr($lastTeacher->teacher_id, 3));
            $newId = 'TCR' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newId = 'TCR0001';
        }

        // Handle profile photo upload
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('teachers', 'public');
        }

        // Create new teacher
        MstTeacher::create([
            'teacher_id'   => $newId, // <-- ID dimasukkan di sini
            'first_name'   => $validated['first_name'],
            'last_name'    => $validated['last_name'],
            'npk'          => $validated['npk'],
            'password'     => Hash::make($validated['password']),
            'gender'       => $validated['gender'] ?? null,
            'birth_place'  => $validated['birth_place'] ?? null,
            'birth_date'   => $validated['birth_date'] ?? null,
            'profile_photo'=> $profilePhotoPath,
            'address'      => $validated['address'] ?? null,
            'phone'        => $validated['phone'] ?? null,
            'entry_date'   => $validated['entry_date'] ?? date('Y-m-d'),
            'level'        => $validated['level'] ?? 'Teacher',
            'status'       => $validated['status'] ?? 'Active',
            'created_by'   => session('employee_id') ?? 'SYSTEM',
            'updated_by'   => session('employee_id') ?? 'SYSTEM'
        ]);

        Log::info('Teacher created successfully');

        return redirect()->route('employee.teachers.index')
            ->with('success', 'Teacher created successfully!');
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Validation error while creating teacher: ' . json_encode($e->errors()));
        return back()->withInput()->withErrors($e->errors());
    } catch (\Exception $e) {
        Log::error('Error creating teacher: ' . $e->getMessage());
        return back()->withInput()->with('error', 'Error creating teacher: ' . $e->getMessage());
    }
}

    /**
     * Show the form for editing the specified teacher
     * 
     * @param string $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            // Fetch teacher by ID using Eloquent
            $teacher = MstTeacher::findOrFail($id);
            return view('teachers.edit', compact('teacher'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Teacher not found: ' . $id);
            return redirect()->route('employee.teachers.index')
                ->with('error', 'Teacher not found!');
        } catch (\Exception $e) {
            Log::error('Error fetching teacher: ' . $e->getMessage());
            return redirect()->route('employee.teachers.index')
                ->with('error', 'Error fetching teacher data');
        }
    }

    /**
     * Update the specified teacher in database
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
                'npk' => 'required|string|max:50|unique:mst_teachers,npk,' . $id . ',teacher_id',
                'password' => 'nullable|string|min:6|confirmed',
                'gender' => 'nullable|in:M,F',
                'birth_place' => 'nullable|string|max:100',
                'birth_date' => 'nullable|date',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'entry_date' => 'nullable|date',
                'level' => 'nullable|string|max:50',
                'status' => 'nullable|in:Active,Inactive'
            ]);

            // Get teacher and update
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
                'status' => $validated['status'] ?? $teacher->status,
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ];

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $updateData['profile_photo'] = $request->file('profile_photo')->store('teachers', 'public');
            }

            // Only update password if provided
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $teacher->update($updateData);

            Log::info('Teacher updated successfully: ' . $id);

            return redirect()->route('employee.teachers.index')
                ->with('success', 'Teacher updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error while updating teacher: ' . json_encode($e->errors()));
            return back()->withInput()->withErrors($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Teacher not found: ' . $id);
            return redirect()->route('employee.teachers.index')
                ->with('error', 'Teacher not found!');
        } catch (\Exception $e) {
            Log::error('Error updating teacher: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating teacher: ' . $e->getMessage());
        }
    }

    /**
     * Delete the specified teacher from database
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            // Get teacher and mark as inactive
            $teacher = MstTeacher::findOrFail($id);

            $teacher->update([
                'status' => 'Inactive',
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ]);

            Log::info('Teacher deleted: ' . $id);

            return redirect()->route('employee.teachers.index')
                ->with('success', 'Teacher deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Teacher not found: ' . $id);
            return redirect()->route('employee.teachers.index')
                ->with('error', 'Teacher not found!');
        } catch (\Exception $e) {
            Log::error('Error deleting teacher: ' . $e->getMessage());
            return redirect()->route('employee.teachers.index')
                ->with('error', 'Error deleting teacher: ' . $e->getMessage());
        }
    }
}

