<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'nationality',
        'gender',
        'resume_path',
        'visa_status',
        'japanese_level',
        'current_location',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // A candidate profile belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A candidate can have many applications
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    // A candidate can speak many languages
    public function languages()
    {
        return $this->hasMany(Language::class);
    }
}