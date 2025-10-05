<?php

namespace App\Services;

use App\Services\Contracts\DoctorServiceInterface;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DoctorService implements DoctorServiceInterface
{
    private $doctorRepository;

    public function __construct(DoctorRepositoryInterface $doctorRepository)
    {
        $this->doctorRepository = $doctorRepository;
    }

    /**
     * Devuelve el listado de doctores.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllDoctors(): Collection
    {
        return $this->doctorRepository->getAllDoctors();
    }

    /**
     * Devuelve un doctor por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function getDoctorById(int $id): Model
    {
        return $this->doctorRepository->getDoctorById($id);
    }

    /**
     * Crea un nuevo doctor.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createDoctor(array $data): Model
    {
        return $this->doctorRepository->createDoctor($data);
    }

    /**
     * Actualiza un doctor.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updateDoctor(int $id, array $data): bool
    {
        return $this->doctorRepository->updateDoctor($id, $data);
    }

    /**
     * Elimina un doctor.
     * @param int $id
     * @return bool
    */
    public function deleteDoctor(int $id): bool
    {
        return $this->doctorRepository->deleteDoctor($id);
    }
}
