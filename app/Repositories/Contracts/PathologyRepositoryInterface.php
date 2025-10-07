<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface PathologyRepositoryInterface
{
    /**
     * Devuelve el listado de patologías.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllPathologies(): Collection;

    /**
     * Devuelve una patología por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function getPathologyById(int $id): Model;

    /**
     * Crea una nueva patología.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createPathology(array $data): Model;

    /**
     * Actualiza una patología.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updatePathology(int $id, array $data): bool;

    /**
     * Elimina una patología.
     * @param int $id
     * @return bool
    */
    public function deletePathology(int $id): bool;
}
