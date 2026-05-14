<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Automation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'workflow_id',
        'organization_id',
        'status',
        'trigger_type',
        'action_type',
        'config',
        'is_active',
        'last_executed_at',
        'execution_count',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_active' => 'boolean',
            'last_executed_at' => 'datetime',
        ];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
