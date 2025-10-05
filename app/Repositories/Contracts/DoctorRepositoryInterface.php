<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface DoctorRepositoryInterface
{
    /**
     * Devuelve el listado de doctores.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllDoctors(): Collection;

    /**
     * Devuelve un doctor por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function getDoctorById(int $id): Model;

    /**
     * Crea un nuevo doctor.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createDoctor(array $data): Model;

    /**
     * Actualiza un doctor.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updateDoctor(int $id, array $data): bool;

    /**
     * Elimina un doctor.
     * @param int $id
     * @return bool
    */
    public function deleteDoctor(int $id): bool;
}
