<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    // Display a listing of the resource.
    public function index(Request $request)
    {
        $query = Application::with(['job', 'candidate']);

        // Example filtering: /api/applications?job_id=1
        if ($request->has('job_id')) {
            $query->where('job_id', $request->job_id);
        }

        // Example filtering: /api/applications?candidate_id=1
        if ($request->has('candidate_id')) {
            $query->where('candidate_id', $request->candidate_id);
        }

        return response()->json($query->latest()->paginate(15));
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required|exists:job,id',
            'candidate_id' => 'required|exists:candidates,id',
            'cover_letter' => 'nullable|string',
        ]);
        
        // Add default status
        $validated['status'] = 'pending';

        $application = Application::create($validated);
        return response()->json($application->load(['job', 'candidate']), 201);
    }

    // Display the specified resource.
    public function show(Application $application)
    {
        return response()->json($application->load(['job', 'candidate']));
    }

    // Update the specified resource in storage.
    public function update(Request $request, Application $application)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'reviewed', 'accepted', 'rejected'])],
        ]);

        $application->update($validated);
        return response()->json($application);
    }

    // Remove the specified resource from storage.
    public function destroy(Application $application)
    {
        $application->delete();
        return response()->json(null, 204);
    }
}