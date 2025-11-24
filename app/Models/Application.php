<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'candidate_id',
        'status',
        'cover_letter',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    // An application belongs to a job
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    // An application belongs to a candidate
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}