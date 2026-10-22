<?php

namespace App\Http\Controllers;

use App\Models\StudentModel;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // Display all students
    public function index()
    {
        $students = StudentModel::all();

        return view('students.index', compact('students'));
    }

    // Store a new student
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'year' => 'required|string|max:50',
            'section' => 'required|string|max:100',
        ]);

        StudentModel::create($request->only([
            'firstname',
            'middlename',
            'lastname',
            'year',
            'section',
        ]));

        return redirect()->route('students.index')
            ->with('success', 'Student added successfully.');
    }

    // Display one student
    public function show(string $id)
    {
        $student = StudentModel::findOrFail($id);

        return view('students.show', compact('student'));
    }

    // Update a student
    public function update(Request $request, string $id)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'year' => 'required|string|max:50',
            'section' => 'required|string|max:100',
        ]);

        $student = StudentModel::findOrFail($id);

        $student->update($request->only([
            'firstname',
            'middlename',
            'lastname',
            'year',
            'section',
        ]));

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    // Delete a student
    public function destroy(string $id)
    {
        $student = StudentModel::findOrFail($id);

        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }
}