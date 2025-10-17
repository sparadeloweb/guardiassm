<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'shifts';

    protected $fillable = [
        'doctor_id',
        'shift_type_id',
        'hourly_rate_snapshot',
        'per_patient_rate_snapshot',
        'starts_at',
        'ends_at',
        'total_hours',
        'patients_count',
        'total_amount',
        'notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'paid_at' => 'datetime',
            'hourly_rate_snapshot' => 'decimal:2',
            'per_patient_rate_snapshot' => 'decimal:2',
            'total_hours' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'patients_count' => 'integer',
            'doctor_id' => 'integer',
            'shift_type_id' => 'integer',
        ];
    }

    /**
     * Obtiene el doctor que el turno pertenece.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Obtiene el tipo de turno que el turno pertenece.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shiftType(): BelongsTo
    {
        return $this->belongsTo(ShiftType::class, 'shift_type_id');
    }

    /**
     * Obtiene las atenciones que el turno tiene.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attentions(): HasMany
    {
        return $this->hasMany(Attention::class);
    }
}
