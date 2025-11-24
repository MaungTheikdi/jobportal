<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_description',
        'website',
        'logo_path',
        'address',
    ];

    // An employer profile belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // An employer can post many jobs
    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}