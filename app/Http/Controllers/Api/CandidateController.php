<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Eager load relationships for better performance on the frontend
        $candidates = Candidate::with(['user', 'languages'])->latest()->paginate(15);
        return response()->json($candidates);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:candidates,user_id',
            'date_of_birth' => 'nullable|date|before:today',
            'nationality' => 'nullable|string|max:100',
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'resume_path' => 'nullable|string|max:255',
            'visa_status' => 'nullable|string|max:100',
            'japanese_level' => ['required', Rule::in(['N1', 'N2', 'N3', 'N4', 'N5', 'None'])],
            'current_location' => 'nullable|string|max:255',
        ]);

        $candidate = Candidate::create($validated);

        return response()->json($candidate->load('user'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Candidate  $candidate
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Candidate $candidate)
    {
        // Load all relevant relationships for a detailed view
        return response()->json($candidate->load(['user', 'applications', 'languages']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Candidate  $candidate
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            // user_id should not be updatable
            'date_of_birth' => 'sometimes|nullable|date|before:today',
            'nationality' => 'sometimes|nullable|string|max:100',
            'gender' => ['sometimes', 'nullable', Rule::in(['male', 'female', 'other'])],
            'resume_path' => 'sometimes|nullable|string|max:255',
            'visa_status' => 'sometimes|nullable|string|max:100',
            'japanese_level' => ['sometimes', 'required', Rule::in(['N1', 'N2', 'N3', 'N4', 'N5', 'None'])],
            'current_location' => 'sometimes|nullable|string|max:255',
        ]);

        $candidate->update($validated);

        return response()->json($candidate->load('user'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Candidate  $candidate
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Candidate $candidate)
    {
        $candidate->delete();

        return response()->json(null, 204); // 204 No Content
    }
}