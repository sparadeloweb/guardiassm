<?php

namespace App\Repositories;

use App\Repositories\Contracts\ShiftRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Models\Shift;

class ShiftRepository implements ShiftRepositoryInterface
{
    private $shift;

    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    /**
     * Devuelve el listado de turnos.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllShifts(): Collection
    {
        return $this->shift->with('doctor', 'shiftType')->get();
    }

    /**
     * Devuelve un turno por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function getShiftById(int $id): ?Model
    {
        return $this->shift->with('doctor', 'shiftType', 'attentions', 'attentions.patient', 'attentions.pathologies')->find($id);
    }

    /**
     * Crea un nuevo turno.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createShift(array $data): Model
    {
        return $this->shift->create($data);
    }

    /**
     * Actualiza un turno.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updateShift(int $id, array $data): bool
    {
        $shift = $this->shift->find($id);
        if (!$shift) {
            return false;
        }
        return $shift->update($data);
    }

    /**
     * Elimina un turno.
     * @param int $id
     * @return bool
    */
    public function deleteShift(int $id): bool
    {
        $shift = $this->shift->find($id);
        if (!$shift) {
            return false;
        }
        return $shift->delete();
    }

    /**
     * Obtiene turnos solapados para un doctor en un rango de fechas.
     * @param int $doctorId
     * @param string $startsAt
     * @param string $endsAt
     * @param int|null $excludeShiftId
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getOverlappingShifts(int $doctorId, string $startsAt, string $endsAt, ?int $excludeShiftId = null): Collection
    {
        $query = $this->shift->where('doctor_id', $doctorId)
            ->where(function ($q) use ($startsAt, $endsAt) {
                $q->whereBetween('starts_at', [$startsAt, $endsAt])
                  ->orWhereBetween('ends_at', [$startsAt, $endsAt])
                  ->orWhere(function ($subQ) use ($startsAt, $endsAt) {
                      $subQ->where('starts_at', '<=', $startsAt)
                           ->where('ends_at', '>=', $endsAt);
                  });
            });

        if ($excludeShiftId) {
            $query->where('id', '!=', $excludeShiftId);
        }

        return $query->get();
    }
}
