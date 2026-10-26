<?php

namespace App\Http\Controllers;

use App\Models\StudentModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    // Display all students
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Students retrieved successfully.',
            'data' => StudentModel::all()
        ], 200);
    }

    // Store a new student
    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:students,email',
            'course' => 'required|string|max:255',
            'year' => 'required|string|max:50',
            'section' => 'required|string|max:100',
        ], [
            'email.unique' => 'This email address is already registered.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        $student = StudentModel::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student added successfully.',
            'data' => $student
        ], 201);
    }

    // Display one student
    public function show(string $id)
    {
        $student = StudentModel::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Student retrieved successfully.',
            'data' => $student
        ], 200);
    }

    // Update a student
    public function update(Request $request, string $id)
    {
        $student = StudentModel::findOrFail($id);

        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student->id),
            ],
            'course' => 'required|string|max:255',
            'year' => 'required|string|max:50',
            'section' => 'required|string|max:100',
        ], [
            'email.unique' => 'This email address is already registered.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully.',
            'data' => $student->fresh()
        ], 200);
    }

    // Delete a student
    public function destroy(string $id)
    {
        $student = StudentModel::findOrFail($id);

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully.'
        ], 200);
    }
}