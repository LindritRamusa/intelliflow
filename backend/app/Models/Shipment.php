<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'origin',
        'destination',
        'cargo_type',
        'cargo_weight_kg',
        'priority',
        'status',
        'driver_name',
        'vehicle_id',
        'estimated_arrival',
        'actual_arrival',
        'distance_km',
        'cost_estimate',
        'optimized_route',
        'ai_notes',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'optimized_route' => 'array',
            'cargo_weight_kg' => 'decimal:2',
            'distance_km' => 'decimal:2',
            'cost_estimate' => 'decimal:2',
            'estimated_arrival' => 'date',
            'actual_arrival' => 'date',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
