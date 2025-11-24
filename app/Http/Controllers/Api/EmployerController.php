<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployerController extends Controller
{
    /**
     * Display a listing of employers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Eager load the user relationship to avoid N+1 query problems
        $employers = Employer::with('user')->latest()->paginate(15);
        return response()->json($employers);
    }

    /**
     * Store a newly created employer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:employers,user_id',
            'company_name' => 'required|string|max:255',
            'company_description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'logo_path' => 'nullable|string|max:255', // In a real app, this would be a file upload
            'address' => 'nullable|string',
        ]);

        $employer = Employer::create($validated);

        return response()->json($employer->load('user'), 201); // Return with user data, status 201 Created
    }

    /**
     * Display the specified employer.
     *
     * @param  \App\Models\Employer  $employer
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Employer $employer)
    {
        // Load related user and jobs for a detailed view
        return response()->json($employer->load(['user', 'jobs']));
    }

    /**
     * Update the specified employer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employer  $employer
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Employer $employer)
    {
        $validated = $request->validate([
            // 'user_id' is generally not updatable
            'company_name' => 'sometimes|required|string|max:255',
            'company_description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'logo_path' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        //$employer->update($validated);
        Employer::where('user_id', $request->user_id)->update($validated);

        return response()->json($employer->load('user'));
    }

    /**
     * Remove the specified employer from storage.
     *
     * @param  \App\Models\Employer  $employer
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Employer $employer)
    {
        //$employer->delete();
        Employer::where('id', $employer->id)->delete();

        return response()->json(null, 204); // Respond with 204 No Content
    }
}