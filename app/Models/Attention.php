<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attention extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attentions';

    protected $fillable = [
        'patient_id',
        'shift_id',
        'attended_at',
        'notes',
        'per_patient_amount',
    ];

    protected function casts(): array
    {
        return [
            'attended_at' => 'datetime',
            'per_patient_amount' => 'decimal:2',
        ];
    }

    /**
     * Obtiene el paciente que la atención pertenece.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Obtiene el turno que la atención pertenece.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Obtiene las patologías que la atención tiene.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    */
    public function pathologies(): BelongsToMany
    {
        return $this->belongsToMany(Pathology::class, 'attention_pathology', 'attention_id', 'pathology_id');
    }
}
