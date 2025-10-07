<?php

namespace App\Services;

use App\Services\Contracts\PatientServiceInterface;
use App\Repositories\Contracts\PatientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PatientService implements PatientServiceInterface
{
    private $patientRepository;

    public function __construct(PatientRepositoryInterface $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }

    /**
     * Devuelve el listado de pacientes.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllPatients(): Collection
    {
        return $this->patientRepository->getAllPatients();
    }

    /**
     * Devuelve un paciente por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function getPatientById(int $id): Model
    {
        return $this->patientRepository->getPatientById($id);
    }

    /**
     * Crea un nuevo paciente.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createPatient(array $data): Model
    {
        return $this->patientRepository->createPatient($data);
    }

    /**
     * Actualiza un paciente.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updatePatient(int $id, array $data): bool
    {
        return $this->patientRepository->updatePatient($id, $data);
    }

    /**
     * Elimina un paciente.
     * @param int $id
     * @return bool
    */
    public function deletePatient(int $id): bool
    {
        return $this->patientRepository->deletePatient($id);
    }
}
