<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface PatientRepositoryInterface
{
    /**
     * Devuelve el listado de pacientes.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllPatients(): Collection;

    /**
     * Devuelve un paciente por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function getPatientById(int $id): Model;

    /**
     * Crea un nuevo paciente.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createPatient(array $data): Model;

    /**
     * Actualiza un paciente.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updatePatient(int $id, array $data): bool;

    /**
     * Elimina un paciente.
     * @param int $id
     * @return bool
    */
    public function deletePatient(int $id): bool;
}
