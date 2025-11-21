<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MstClass;
use App\Models\MstTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * ClassController
 * 
 * Handles CRUD operations for classes
 * Attributes: class_id, class_name, class_level, homeroom_teacher_id,
 *             created_at, updated_at, created_by, updated_by
 */
class ClassController extends Controller
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
     * Display list of classes
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Fetch all classes ordered by creation date
            $classes = MstClass::orderBy('created_at', 'DESC')->get();
            return view('classes.index', compact('classes'));
        } catch (\Exception $e) {
            Log::error('Error fetching classes: ' . $e->getMessage());
            return view('classes.index', ['classes' => collect([])]);
        }
    }

    /**
     * Show form to create new class
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            // Get list of active teachers
            $teachers = MstTeacher::where('status', 'Active')
                ->orderBy('first_name', 'ASC')
                ->get();
            return view('classes.create', compact('teachers'));
        } catch (\Exception $e) {
            Log::error('Error fetching teachers: ' . $e->getMessage());
            return view('classes.create', ['teachers' => collect([])]);
        }
    }

    /**
     * Store new class
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
{
    try {
        // Validate input
        $validated = $request->validate([
            'class_name' => 'required|string|max:100',
            'class_level' => 'required|string|max:50',
            'homeroom_teacher_id' => 'nullable|string'
        ]);

        // Generate class_id otomatis
        $lastClass = MstClass::orderBy('class_id', 'DESC')->first();

        if ($lastClass) {
            // Ambil angka belakang -> +1
            $lastNumber = intval(substr($lastClass->class_id, 3));
            $newId = 'CLS' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // Jika belum ada data
            $newId = 'CLS0001';
        }

        // Create class
        MstClass::create([
            'class_id' => $newId,   // ← wajib simpan ini
            'class_name' => $validated['class_name'],
            'class_level' => $validated['class_level'],
            'homeroom_teacher_id' => $validated['homeroom_teacher_id'] ?? null,
            'created_by' => session('employee_id') ?? 'SYSTEM',
            'updated_by' => session('employee_id') ?? 'SYSTEM'
        ]);

        return redirect()
            ->route('employee.classes.index')
            ->with('success', 'Class created successfully!');
            
    } catch (\Illuminate\Validation\ValidationException $e) {
        return back()->withInput()->withErrors($e->errors());
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Error creating class: ' . $e->getMessage());
    }
}


    /**
     * Show form to edit class
     * 
     * @param string $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            // Fetch class by ID
            $class = MstClass::findOrFail($id);
            
            // Get list of active teachers
            $teachers = MstTeacher::where('status', 'Active')
                ->orderBy('first_name', 'ASC')
                ->get();
            
            return view('classes.edit', compact('class', 'teachers'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Class not found: ' . $id);
            return redirect()->route('employee.classes.index')
                ->with('error', 'Class not found!');
        } catch (\Exception $e) {
            Log::error('Error fetching class: ' . $e->getMessage());
            return redirect()->route('employee.classes.index')
                ->with('error', 'Error fetching class data');
        }
    }

    /**
     * Update the specified class
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
                'class_name' => 'required|string|max:100',
                'class_level' => 'required|string|max:50',
                'homeroom_teacher_id' => 'nullable|string|max:50'
            ]);

            // Get class and update
            $class = MstClass::findOrFail($id);

            $class->update([
                'class_name' => $validated['class_name'],
                'class_level' => $validated['class_level'],
                'homeroom_teacher_id' => $validated['homeroom_teacher_id'] ?? $class->homeroom_teacher_id,
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ]);

            Log::info('Class updated successfully: ' . $id);

            return redirect()->route('employee.classes.index')
                ->with('success', 'Class updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error while updating class: ' . json_encode($e->errors()));
            return back()->withInput()->withErrors($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Class not found: ' . $id);
            return redirect()->route('employee.classes.index')
                ->with('error', 'Class not found!');
        } catch (\Exception $e) {
            Log::error('Error updating class: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating class: ' . $e->getMessage());
        }
    }

    /**
     * Delete the specified class
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            // Get class and delete
            $class = MstClass::findOrFail($id);
            $class->delete();

            Log::info('Class deleted: ' . $id);

            return redirect()->route('employee.classes.index')
                ->with('success', 'Class deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Class not found: ' . $id);
            return redirect()->route('employee.classes.index')
                ->with('error', 'Class not found!');
        } catch (\Exception $e) {
            Log::error('Error deleting class: ' . $e->getMessage());
            return redirect()->route('employee.classes.index')
                ->with('error', 'Error deleting class: ' . $e->getMessage());
        }
    }
}
