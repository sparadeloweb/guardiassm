<?php

namespace App\Repositories;

use App\Repositories\Contracts\ShiftTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Models\ShiftType;

class ShiftTypeRepository implements ShiftTypeRepositoryInterface
{
    private $shiftType;

    public function __construct(ShiftType $shiftType)
    {
        $this->shiftType = $shiftType;
    }

    /**
     * Devuelve el listado de tipos de turnos.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllShiftTypes(): Collection
    {
        return $this->shiftType->all();
    }

    /**
     * Devuelve un tipo de turno por su ID con todas sus relaciones.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getShiftTypeById(int $id): Model
    {
        return $this->shiftType->with([
            'shifts.doctor',
            'shifts.attentions.patient',
            'shifts.attentions.pathologies',
        ])->findOrFail($id);
    }

    /**
     * Crea un nuevo tipo de turno.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createShiftType(array $data): Model
    {
        return $this->shiftType->create($data);
    }

    /**
     * Actualiza un tipo de turno.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updateShiftType(int $id, array $data): bool
    {
        return $this->shiftType->find($id)->update($data);
    }

    /**
     * Elimina un tipo de turno.
     * @param int $id
     * @return bool
    */
    public function deleteShiftType(int $id): bool
    {
        return $this->shiftType->find($id)->delete();
    }
}
