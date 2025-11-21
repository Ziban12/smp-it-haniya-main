<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MstStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Student Management Controller
 * 
 * Handles CRUD operations for student management.
 * Attributes: student_id, first_name, last_name, nis, birth_date, birth_place, gender,
 *             address, father_name, mother_name, father_phone, mother_phone, father_job,
 *             mother_job, password, entry_date, graduation_date, profile_photo, status,
 *             created_at, updated_at, created_by, updated_by
 */
class StudentController extends Controller
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
     * Display list of students
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Fetch all active students ordered by creation date
            $students = MstStudent::where('status', 'Active')
                ->orderBy('created_at', 'DESC')
                ->get();

            return view('students.index', compact('students'));
        } catch (\Exception $e) {
            Log::error('Error fetching students: ' . $e->getMessage());
            return view('students.index', ['students' => collect([])]);
        }
    }

    /**
     * Show the form for creating a new student
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created student in database
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
            'nis' => 'required|string|max:50|unique:mst_students,nis',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'nullable|in:M,F',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:100',
            'father_phone' => 'nullable|string|max:20',
            'mother_phone' => 'nullable|string|max:20',
            'father_job' => 'nullable|string|max:100',
            'mother_job' => 'nullable|string|max:100',
            'entry_date' => 'nullable|date',
            'graduation_date' => 'nullable|date',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:Active,Inactive'
        ]);

        // ==== Generate Student ID ====
        $lastStudent = MstStudent::orderBy('student_id', 'DESC')->first();
        if ($lastStudent) {
            $lastNumber = intval(substr($lastStudent->student_id, 3));
            $newId = 'STD' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newId = 'STD0001';
        }

        // Handle profile photo upload
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('students', 'public');
        }

        // Create new student
        MstStudent::create([
            'student_id' => $newId, // ⬅ penting dimasukkan
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'nis' => $validated['nis'],
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'address' => $validated['address'] ?? null,
            'father_name' => $validated['father_name'] ?? null,
            'mother_name' => $validated['mother_name'] ?? null,
            'father_phone' => $validated['father_phone'] ?? null,
            'mother_phone' => $validated['mother_phone'] ?? null,
            'father_job' => $validated['father_job'] ?? null,
            'mother_job' => $validated['mother_job'] ?? null,
            'entry_date' => $validated['entry_date'] ?? date('Y-m-d'),
            'graduation_date' => $validated['graduation_date'] ?? null,
            'profile_photo' => $profilePhotoPath,
            'status' => $validated['status'] ?? 'Active',
            'created_by' => session('employee_id') ?? 'SYSTEM',
            'updated_by' => session('employee_id') ?? 'SYSTEM'
        ]);

        Log::info('Student created successfully');

        return redirect()->route('employee.students.index')
            ->with('success', 'Student created successfully!');
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Validation error while creating student: ' . json_encode($e->errors()));
        return back()->withInput()->withErrors($e->errors());
    } catch (\Exception $e) {
        Log::error('Error creating student: ' . $e->getMessage());
        return back()->withInput()->with('error', 'Error creating student: ' . $e->getMessage());
    }
}


    /**
     * Show the form for editing the specified student
     * 
     * @param string $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            // Fetch student by ID using Eloquent
            $student = MstStudent::findOrFail($id);
            return view('students.edit', compact('student'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Student not found: ' . $id);
            return redirect()->route('employee.students.index')
                ->with('error', 'Student not found!');
        } catch (\Exception $e) {
            Log::error('Error fetching student: ' . $e->getMessage());
            return redirect()->route('employee.students.index')
                ->with('error', 'Error fetching student data');
        }
    }

    /**
     * Update the specified student in database
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
                'nis' => 'required|string|max:50|unique:mst_students,nis,' . $id . ',student_id',
                'password' => 'nullable|string|min:6|confirmed',
                'gender' => 'nullable|in:M,F',
                'birth_place' => 'nullable|string|max:100',
                'birth_date' => 'nullable|date',
                'address' => 'nullable|string|max:255',
                'father_name' => 'nullable|string|max:100',
                'mother_name' => 'nullable|string|max:100',
                'father_phone' => 'nullable|string|max:20',
                'mother_phone' => 'nullable|string|max:20',
                'father_job' => 'nullable|string|max:100',
                'mother_job' => 'nullable|string|max:100',
                'entry_date' => 'nullable|date',
                'graduation_date' => 'nullable|date',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'nullable|in:Active,Inactive'
            ]);

            // Get student and update
            $student = MstStudent::findOrFail($id);

            $updateData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'nis' => $validated['nis'],
                'gender' => $validated['gender'] ?? $student->gender,
                'birth_place' => $validated['birth_place'] ?? $student->birth_place,
                'birth_date' => $validated['birth_date'] ?? $student->birth_date,
                'address' => $validated['address'] ?? $student->address,
                'father_name' => $validated['father_name'] ?? $student->father_name,
                'mother_name' => $validated['mother_name'] ?? $student->mother_name,
                'father_phone' => $validated['father_phone'] ?? $student->father_phone,
                'mother_phone' => $validated['mother_phone'] ?? $student->mother_phone,
                'father_job' => $validated['father_job'] ?? $student->father_job,
                'mother_job' => $validated['mother_job'] ?? $student->mother_job,
                'entry_date' => $validated['entry_date'] ?? $student->entry_date,
                'graduation_date' => $validated['graduation_date'] ?? $student->graduation_date,
                'status' => $validated['status'] ?? $student->status,
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ];

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $updateData['profile_photo'] = $request->file('profile_photo')->store('students', 'public');
            }

            // Only update password if provided
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $student->update($updateData);

            Log::info('Student updated successfully: ' . $id);

            return redirect()->route('employee.students.index')
                ->with('success', 'Student updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error while updating student: ' . json_encode($e->errors()));
            return back()->withInput()->withErrors($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Student not found: ' . $id);
            return redirect()->route('employee.students.index')
                ->with('error', 'Student not found!');
        } catch (\Exception $e) {
            Log::error('Error updating student: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating student: ' . $e->getMessage());
        }
    }

    /**
     * Delete the specified student from database
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            // Get student and mark as inactive
            $student = MstStudent::findOrFail($id);

            $student->update([
                'status' => 'Inactive',
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ]);

            Log::info('Student deleted: ' . $id);

            return redirect()->route('employee.students.index')
                ->with('success', 'Student deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Student not found: ' . $id);
            return redirect()->route('employee.students.index')
                ->with('error', 'Student not found!');
        } catch (\Exception $e) {
            Log::error('Error deleting student: ' . $e->getMessage());
            return redirect()->route('employee.students.index')
                ->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }
}
