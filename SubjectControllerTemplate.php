<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MstSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * SubjectController
 * 
 * Handles CRUD operations for subjects
 * Attributes: subject_id, subject_name, subject_code, class_level, description,
 *             created_at, updated_at, created_by, updated_by
 */
class SubjectControllerNew extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!session('user_type') || session('user_type') !== 'employee') {
                return redirect()->route('employee.login');
            }
            return $next($request);
        });
    }

    public function index()
    {
        try {
            $subjects = MstSubject::orderBy('created_at', 'DESC')->get();
            return view('subjects.index', compact('subjects'));
        } catch (\Exception $e) {
            Log::error('Error fetching subjects: ' . $e->getMessage());
            return view('subjects.index', ['subjects' => collect([])]);
        }
    }

    public function create()
    {
        return view('subjects.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'subject_id' => 'required|string|max:50|unique:mst_subjects,subject_id',
                'subject_name' => 'required|string|max:100',
                'subject_code' => 'required|string|max:50|unique:mst_subjects,subject_code',
                'class_level' => 'required|string|max:50',
                'description' => 'nullable|string|max:500'
            ]);

            MstSubject::create([
                'subject_id' => $validated['subject_id'],
                'subject_name' => $validated['subject_name'],
                'subject_code' => $validated['subject_code'],
                'class_level' => $validated['class_level'],
                'description' => $validated['description'] ?? null,
                'created_by' => session('employee_id') ?? 'SYSTEM',
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ]);

            Log::info('Subject created successfully: ' . $validated['subject_id']);
            return redirect()->route('employee.subjects.index')
                ->with('success', 'Subject created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error: ' . json_encode($e->errors()));
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error creating subject: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $subject = MstSubject::findOrFail($id);
            return view('subjects.edit', compact('subject'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Subject not found: ' . $id);
            return redirect()->route('employee.subjects.index')
                ->with('error', 'Subject not found!');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'subject_name' => 'required|string|max:100',
                'subject_code' => 'required|string|max:50|unique:mst_subjects,subject_code,' . $id . ',subject_id',
                'class_level' => 'required|string|max:50',
                'description' => 'nullable|string|max:500'
            ]);

            $subject = MstSubject::findOrFail($id);
            $subject->update([
                'subject_name' => $validated['subject_name'],
                'subject_code' => $validated['subject_code'],
                'class_level' => $validated['class_level'],
                'description' => $validated['description'] ?? $subject->description,
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ]);

            Log::info('Subject updated: ' . $id);
            return redirect()->route('employee.subjects.index')
                ->with('success', 'Subject updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error updating subject: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $subject = MstSubject::findOrFail($id);
            $subject->delete();

            Log::info('Subject deleted: ' . $id);
            return redirect()->route('employee.subjects.index')
                ->with('success', 'Subject deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting subject: ' . $e->getMessage());
            return redirect()->route('employee.subjects.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
