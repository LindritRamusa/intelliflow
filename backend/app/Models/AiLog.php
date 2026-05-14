<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_id',
        'prompt',
        'response',
        'model',
        'tokens_used',
        'context',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'tokens_used' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
