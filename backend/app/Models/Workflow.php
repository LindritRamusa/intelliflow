<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'trigger',
        'trigger_config',
        'organization_id',
        'created_by',
        'last_run_at',
        'run_count',
    ];

    protected function casts(): array
    {
        return [
            'trigger_config' => 'array',
            'last_run_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function automations(): HasMany
    {
        return $this->hasMany(Automation::class);
    }
}
