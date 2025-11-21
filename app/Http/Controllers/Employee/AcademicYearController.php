<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MstAcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AcademicYearController extends Controller
{
    /**
     * Pastikan user employee sudah login
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
     * Tampilkan list academic year
     */
    public function index()
    {
        try {
            $academicYears = MstAcademicYear::orderBy('start_date', 'DESC')->get();

            return view('academic_years.index', [
                'academicYears' => $academicYears
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching academic years: ' . $e->getMessage());
            return view('academic_years.index', [
                'academicYears' => collect([]),
            ]);
        }
    }

    /**
     * Form create academic year
     */
    public function create()
    {
        return view('academic_years.create');
    }

    /**
     * Simpan academic year baru
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'semester' => 'required|string|max:20',
                'status' => 'required|in:Active,Inactive',
            ]);

            // Generate ID otomatis
            $lastYear = MstAcademicYear::orderBy('academic_year_id', 'DESC')->first();

            if ($lastYear) {
                $lastNumber = intval(substr($lastYear->academic_year_id, 3));
                $newId = 'ACY' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newId = 'ACY0001';
            }

            MstAcademicYear::create([
                'academic_year_id' => $newId,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'semester' => $validated['semester'],
                'status' => $validated['status'],
                'created_by' => session('employee_id') ?? 'SYSTEM',
                'updated_by' => session('employee_id') ?? 'SYSTEM',
            ]);

            Log::info('Academic year created: ' . $newId);

            return redirect()
                ->route('employee.academic-years.index')
                ->with('success', 'Academic Year created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error creating academic year: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Form edit academic year
     */
    public function edit($id)
    {
        try {
            $academicYear = MstAcademicYear::findOrFail($id);

            return view('academic_years.edit', compact('academicYear'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('employee.academic_years.index')
                ->with('error', 'Academic Year not found!');
        } catch (\Exception $e) {
            Log::error('Error fetching academic year: ' . $e->getMessage());
            return redirect()
                ->route('employee.academic_years.index')
                ->with('error', 'Error fetching academic year!');
        }
    }

    /**
     * Update academic year
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'semester' => 'required|string|max:20',
                'status' => 'required|in:Active,Inactive',
            ]);

            $academicYear = MstAcademicYear::findOrFail($id);

            $academicYear->update([
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'semester' => $validated['semester'],
                'status' => $validated['status'],
                'updated_by' => session('employee_id') ?? 'SYSTEM',
            ]);

            Log::info('Academic year updated: ' . $id);

           return redirect()
                ->route('employee.academic-years.index')
                ->with('success', 'Academic Year created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error updating academic year: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Hapus academic year
     */
    public function destroy($id)
    {
        try {
            $academicYear = MstAcademicYear::findOrFail($id);
            $academicYear->delete();

            return redirect()
                ->route('employee.academic-years.index')
                ->with('success', 'Academic Year created successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting academic year: ' . $e->getMessage());
            return redirect()
                ->route('employee.academic_years.index')
                ->with('error', 'Error deleting academic year!');
        }
    }
}
