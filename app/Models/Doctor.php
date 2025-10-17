<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doctors';

    protected $fillable = [
        'name',
        'age',
        'email',
        'phone',
        'address',
        'is_resident',
    ];

    protected function casts(): array
    {
        return [
            'is_resident' => 'boolean',
            'age' => 'integer',
        ];
    }

    /**
     * Obtiene el nombre completo del doctor.
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
     * Obtiene los turnos que el doctor tiene.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }
}
