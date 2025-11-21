<?php

namespace App\Http\Controllers\Employee;
use App\Http\Controllers\Controller;
use App\Models\MstSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * SubjectController
 * 
 * Handles CRUD operations for subjects
 * - All READ operations (index, edit) use raw SQL SELECT queries
 * - All WRITE operations (store, update, destroy) use Eloquent Models
 */
class SubjectController extends Controller
{
    /**
     * Display list of subjects using raw SELECT query
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check if user is employee
       

        // Raw SELECT query to get all subjects
        $subjects = DB::select('SELECT * FROM mst_subjects ORDER BY subject_id DESC');

        return view('subjects.index', ['subjects' => $subjects]);
    }

    /**
     * Show form to create new subject
     * 
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        // Check if user is employee

        return view('subjects.create');
    }

    /**
     * Store new subject using Eloquent Model
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
 public function store(Request $request)
{
    try {
        // Validate input
        $validated = $request->validate([
            'subject_name' => 'required|string|max:100',
            'subject_code' => 'required|string|max:20|unique:mst_subjects,subject_code',
            'class_level'  => 'required|integer|min:1|max:12',
            'description'  => 'nullable|string|max:500',
            'status'       => 'nullable|in:Active,Inactive'
        ]);

        // ==== Generate Subject ID ====
        $lastSubject = MstSubject::orderBy('subject_id', 'DESC')->first();
        if ($lastSubject) {
            $lastNumber = intval(substr($lastSubject->subject_id, 3));
            $newId = 'SBJ' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newId = 'SBJ0001';
        }

        // Create new subject
        MstSubject::create([
            'subject_id'   => $newId,
            'subject_name' => $validated['subject_name'],
            'subject_code' => $validated['subject_code'],
            'class_level'  => $validated['class_level'],
            'description'  => $validated['description'] ?? null,
            'status'       => $validated['status'] ?? 'Active',
            'created_by'   => session('employee_id') ?? 'SYSTEM',
            'updated_by'   => session('employee_id') ?? 'SYSTEM',
        ]);

    Log::info('Subject created successfully: ' . $newId);

        return redirect()->route('employee.subjects.index')
            ->with('success', 'Subject created successfully!');
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Validation error while creating subject: ' . json_encode($e->errors()));
        return back()->withInput()->withErrors($e->errors());
    } catch (\Exception $e) {
        Log::error('Error creating subject: ' . $e->getMessage());
        return back()->withInput()->with('error', 'Error creating subject: ' . $e->getMessage());
    }
}




    /**
     * Show form to edit subject (fetch data with raw SELECT)
     * 
     * @param string $id Subject ID
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        // Check if user is employee
      
        // Raw SELECT query to get subject
        $subjects = DB::select('SELECT * FROM mst_subjects WHERE subject_id = ?', [$id]);

        if (empty($subjects)) {
            return redirect()->route('employee.subjects.index')
                           ->with('error', 'Subject not found!');
        }

        $subject = $subjects[0];

        return view('subjects.edit', ['subject' => $subject]);
    }

    /**
     * Update subject using Eloquent Model
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $id Subject ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
       

        // Validate input
        $validated = $request->validate([
            'subject_name' => 'required|string|max:100',
            'subject_code' => 'required|string|max:20',
            'class_level' => 'required|string|max:10',
            'description' => 'nullable|string|max:500',
        ]);

        // Add updated_by
        $validated['updated_by'] = session('employee_id');

        try {
            // Find subject using Eloquent
            $subject = MstSubject::findOrFail($id);

            // Update subject
            $subject->update($validated);

            return redirect()->route('employee.subjects.index')
                           ->with('success', 'Subject updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to update subject: ' . $e->getMessage());
        }
    }

    /**
     * Delete subject using Eloquent Model
     * 
     * @param string $id Subject ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Check if user is employee
       
        try {
            // Find and delete subject
            $subject = MstSubject::findOrFail($id);
            $subject->delete();

            return redirect()->route('employee.subjects.index')
                           ->with('success', 'Subject deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete subject: ' . $e->getMessage());
        }
    }
}
