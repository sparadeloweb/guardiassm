<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface AttentionRepositoryInterface
{
    /**
     * Devuelve el listado de atenciones.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllAttentions(): Collection;

    /**
     * Devuelve una atención por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function getAttentionById(int $id): ?Model;

    /**
     * Crea una nueva atención para un turno.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createAttention(array $data): Model;

    /**
     * Actualiza una atención para un turno.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updateAttention(int $id, array $data): bool;

    /**
     * Elimina una atención para un turno.
     * @param int $id
     * @return bool
    */
    public function deleteAttention(int $id): bool;

    /**
     * Asigna patologías a una atención.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function attachPathologies(int $id, array $data): bool;

    /**
     * Desasigna patologías de una atención.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function detachPathologies(int $id, array $data): bool;
}
