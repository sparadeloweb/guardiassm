<?php

namespace App\Repositories;

use App\Repositories\Contracts\PathologyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Models\Pathology;

class PathologyRepository implements PathologyRepositoryInterface
{
    private $pathology;

    public function __construct(Pathology $pathology)
    {
        $this->pathology = $pathology;
    }

    /**
     * Devuelve el listado de patologías.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllPathologies(): Collection
    {
        return $this->pathology->get();
    }

    /**
     * Devuelve una patología por su ID con todas sus relaciones.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getPathologyById(int $id): Model
    {
        return $this->pathology->with([
            'attentions.patient',
            'attentions.shift',
            'attentions.shift.doctor',
            'attentions.shift.shiftType',
        ])->findOrFail($id);
    }

    /**
     * Crea una nueva patología.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createPathology(array $data): Model
    {
        return $this->pathology->create($data);
    }

    /**
     * Actualiza una patología.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updatePathology(int $id, array $data): bool
    {
        return $this->pathology->findOrFail($id)->update($data);
    }

    /**
     * Elimina una patología.
     * @param int $id
     * @return bool
    */
    public function deletePathology(int $id): bool
    {
        return $this->pathology->findOrFail($id)->delete();
    }
}
