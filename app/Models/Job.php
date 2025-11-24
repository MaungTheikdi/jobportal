<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    
    // Explicitly defining table name to 'jobs'
    protected $table = 'job';

    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'location',
        'job_category_id',
        'salary_min',
        'salary_max',
        'currency',
        'employment_type',
        'status',
        'posted_at',
        'deadline',
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'posted_at' => 'datetime',
        'deadline' => 'date',
    ];

    // A job belongs to an employer
    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

    // A job belongs to a category
    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    // A job can have many applications
    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}