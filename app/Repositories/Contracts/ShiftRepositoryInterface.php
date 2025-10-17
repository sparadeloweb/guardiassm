<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ShiftRepositoryInterface
{
    /**
     * Devuelve el listado de turnos.
     */
    public function getAllShifts(): Collection;

    /**
     * Devuelve un turno por su ID.
     */
    public function getShiftById(int $id): ?Model;

    /**
     * Crea un nuevo turno.
     */
    public function createShift(array $data): Model;

    /**
     * Actualiza un turno.
     */
    public function updateShift(int $id, array $data): bool;

    /**
     * Elimina un turno.
     */
    public function deleteShift(int $id): bool;

    /**
     * Obtiene turnos solapados para un doctor en un rango de fechas.
     */
    public function getOverlappingShifts(int $doctorId, string $startsAt, string $endsAt, ?int $excludeShiftId = null): Collection;

    /**
     * Obtiene los turnos de un mes y año específico.
     */
    public function getShiftsByMonthAndYear(int $month, int $year): Collection;
}
