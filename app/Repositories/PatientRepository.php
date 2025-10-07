<?php

namespace App\Repositories;

use App\Repositories\Contracts\PatientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Models\Patient;

class PatientRepository implements PatientRepositoryInterface
{
    private $patient;

    public function __construct(Patient $patient)
    {
        $this->patient = $patient;
    }

    /**
     * Devuelve el listado de pacientes.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllPatients(): Collection
    {
        return $this->patient->all();
    }

    /**
     * Devuelve un paciente por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function getPatientById(int $id): Model
    {
        return $this->patient->find($id);
    }

    /**
     * Crea un nuevo paciente.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createPatient(array $data): Model
    {
        return $this->patient->create($data);
    }

    /**
     * Actualiza un paciente.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updatePatient(int $id, array $data): bool
    {
        return $this->patient->find($id)->update($data);
    }

    /**
     * Elimina un paciente.
     * @param int $id
     * @return bool
    */
    public function deletePatient(int $id): bool
    {
        return $this->patient->find($id)->delete();
    }
}
