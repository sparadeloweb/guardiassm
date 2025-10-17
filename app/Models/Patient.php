<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'patients';

    protected $fillable = [
        'name',
        'DNI',
        'phone',
        'address',
        'health_insurance',
        'email',
        'gender',
        'birth_date',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    /**
     * Obtiene el nombre completo del paciente.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name,
        );
    }

    /**
     * Obtiene las atenciones que el paciente ha recibido.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attentions(): HasMany
    {
        return $this->hasMany(Attention::class);
    }

    /**
     * Obtiene los turnos a través de las atenciones.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function shifts(): HasManyThrough
    {
        return $this->hasManyThrough(Shift::class, Attention::class);
    }
}
