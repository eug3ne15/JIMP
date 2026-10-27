<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'internship_id',
        'course_id',
        'applicant_name',
        'field_of_study',
        'institution',
        'status',
        'document_path',
        'school_recommendation_path',
        'insurance_path',
        'cv_path',
        'supervisor_evaluation_path',
        'recommendation_letter_path',
        'hr_comment',
        'rejection_reason',
        'is_qualified',
    ];

    protected $casts = [
        'is_qualified' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
