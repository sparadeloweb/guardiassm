<?php

namespace App\Repositories;

use App\Repositories\Contracts\DoctorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Models\Doctor;

class DoctorRepository implements DoctorRepositoryInterface
{
    private $doctor;

    public function __construct(Doctor $doctor)
    {
        $this->doctor = $doctor;
    }

    /**
     * Devuelve el listado de doctores.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllDoctors(): Collection
    {
        return $this->doctor->all();
    }

    /**
     * Devuelve un doctor por su ID con todas sus relaciones.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getDoctorById(int $id): Model
    {
        return $this->doctor->with([
            'shifts.shiftType',
            'shifts.attentions.patient',
            'shifts.attentions.pathologies',
        ])->findOrFail($id);
    }

    /**
     * Crea un nuevo doctor.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createDoctor(array $data): Model
    {
        return $this->doctor->create($data);
    }

    /**
     * Actualiza un doctor.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updateDoctor(int $id, array $data): bool
    {
        return $this->doctor->findOrFail($id)->update($data);
    }

    /**
     * Elimina un doctor.
     * @param int $id
     * @return bool
    */
    public function deleteDoctor(int $id): bool
    {
        return $this->doctor->findOrFail($id)->delete();
    }
}
