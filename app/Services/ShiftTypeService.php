<?php

namespace App\Services;

use App\Services\Contracts\ShiftTypeServiceInterface;
use App\Repositories\Contracts\ShiftTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ShiftTypeService implements ShiftTypeServiceInterface
{
    private $shiftTypeRepository;

    public function __construct(ShiftTypeRepositoryInterface $shiftTypeRepository)
    {
        $this->shiftTypeRepository = $shiftTypeRepository;
    }

    /**
     * Devuelve el listado de tipos de turnos.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllShiftTypes(): Collection
    {
        return $this->shiftTypeRepository->getAllShiftTypes();
    }

    /**
     * Devuelve un tipo de turno por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function getShiftTypeById(int $id): Model
    {
        return $this->shiftTypeRepository->getShiftTypeById($id);
    }

    /**
     * Crea un nuevo tipo de turno.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createShiftType(array $data): Model
    {
        return $this->shiftTypeRepository->createShiftType($data);
    }

    /**
     * Actualiza un tipo de turno.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updateShiftType(int $id, array $data): bool
    {
        return $this->shiftTypeRepository->updateShiftType($id, $data);
    }

    /**
     * Elimina un tipo de turno.
     * @param int $id
     * @return bool
    */
    public function deleteShiftType(int $id): bool
    {
        return $this->shiftTypeRepository->deleteShiftType($id);
    }
}
