<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'phone',
        'applied_role',
        'cv_text',
        'ai_score',
        'ai_analysis',
        'ai_strengths',
        'ai_weaknesses',
        'skills',
        'status',
        'notes',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'ai_strengths' => 'array',
            'ai_weaknesses' => 'array',
            'ai_score' => 'integer',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
