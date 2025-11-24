<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        // Eager load relationships for better performance
        $jobs = Job::with(['employer', 'jobCategory'])->latest()->paginate(10);
        return response()->json($jobs);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employer_id' => 'required|exists:employers,id',
            'job_category_id' => 'required|exists:job_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|gte:salary_min',
            'currency' => 'required|string|max:10',
            'employment_type' => ['required', Rule::in(['full-time', 'part-time', 'internship', 'contract'])],
            'status' => ['required', Rule::in(['open', 'closed', 'draft'])],
            'deadline' => 'nullable|date',
        ]);

        $job = Job::create($validated);
        return response()->json($job->load(['employer', 'jobCategory']), 201);
    }

    // Display the specified resource.
    public function show(Job $job)
    {
        return response()->json($job->load(['employer', 'jobCategory', 'applications.candidate']));
    }

    // Update the specified resource in storage.
    public function update(Request $request, Job $job)
    {
        $validated = $request->validate([
            'job_category_id' => 'sometimes|required|exists:job_categories,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'location' => 'sometimes|required|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|gte:salary_min',
            'currency' => 'sometimes|required|string|max:10',
            'employment_type' => ['sometimes', 'required', Rule::in(['full-time', 'part-time', 'internship', 'contract'])],
            'status' => ['sometimes', 'required', Rule::in(['open', 'closed', 'draft'])],
            'deadline' => 'nullable|date',
        ]);

        $job->update($validated);
        return response()->json($job->load(['employer', 'jobCategory']));
    }

    // Remove the specified resource from storage.
    public function destroy(Job $job)
    {
        $job->delete();
        return response()->json(null, 204); // 204 No Content
    }

    //Mobile API
    public function getOpenJobs()
    {
        $jobs = Job::with(['employer', 'jobCategory'])
                    ->where('status', 'open')
                    ->latest()
                    ->paginate(10);
        return response()->json($jobs);
    }
}