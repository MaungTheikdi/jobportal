<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'name',
        'level',
    ];

    // A language entry belongs to a candidate
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}