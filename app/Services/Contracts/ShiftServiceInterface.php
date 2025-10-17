<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ShiftServiceInterface
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
     * Crea un nuevo turno con sus atenciones.
     */
    public function createShift(array $data): Model;

    /**
     * Actualiza un turno existente con sus atenciones.
     */
    public function updateShift(int $id, array $data): bool;

    /**
     * Elimina un turno y todas sus atenciones.
     */
    public function deleteShift(int $id): bool;

    /**
     * Obtiene los turnos para mostrar en el calendario.
     */
    public function getShiftsForCalendar(int $month, int $year): array;
}
