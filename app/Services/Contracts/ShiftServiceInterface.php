<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ShiftServiceInterface
{
    /**
     * Devuelve el listado de turnos.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllShifts(): Collection;

    /**
     * Devuelve un turno por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function getShiftById(int $id): ?Model;

    /**
     * Crea un nuevo turno con sus atenciones.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createShift(array $data): Model;

    /**
     * Actualiza un turno existente con sus atenciones.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updateShift(int $id, array $data): bool;

    /**
     * Elimina un turno y todas sus atenciones.
     * @param int $id
     * @return bool
    */
    public function deleteShift(int $id): bool;
}
