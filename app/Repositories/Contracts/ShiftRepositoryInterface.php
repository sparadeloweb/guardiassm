<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ShiftRepositoryInterface
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
     * Crea un nuevo turno.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createShift(array $data): Model;

    /**
     * Actualiza un turno.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updateShift(int $id, array $data): bool;

    /**
     * Elimina un turno.
     * @param int $id
     * @return bool
    */
    public function deleteShift(int $id): bool;

    /**
     * Obtiene turnos solapados para un doctor en un rango de fechas.
     * @param int $doctorId
     * @param string $startsAt
     * @param string $endsAt
     * @param int|null $excludeShiftId
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getOverlappingShifts(int $doctorId, string $startsAt, string $endsAt, ?int $excludeShiftId = null): Collection;
}
