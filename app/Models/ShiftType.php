<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'shift_types';

    protected $fillable = [
        'name',
        'description',
        'value',
        'patient_value',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'patient_value' => 'decimal:2',
        ];
    }

    /**
     * Obtiene los turnos que pertenecen a este tipo de turno.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }
}
