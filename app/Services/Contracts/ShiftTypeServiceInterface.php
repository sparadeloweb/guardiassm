<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ShiftTypeServiceInterface
{
    /**
     * Devuelve el listado de tipos de turnos.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllShiftTypes(): Collection;

    /**
     * Devuelve un tipo de turno por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
    */

    /**
     * Devuelve un tipo de turno por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function getShiftTypeById(int $id): Model;

    /**
     * Crea un nuevo tipo de turno.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createShiftType(array $data): Model;

    /**
     * Actualiza un tipo de turno.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updateShiftType(int $id, array $data): bool;

    /**
     * Elimina un tipo de turno.
     * @param int $id
     * @return bool
    */
    public function deleteShiftType(int $id): bool;
}
