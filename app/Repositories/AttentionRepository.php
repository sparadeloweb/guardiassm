<?php

namespace App\Repositories;

use App\Repositories\Contracts\AttentionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Models\Attention;

class AttentionRepository implements AttentionRepositoryInterface
{
    private $attention;

    public function __construct(Attention $attention)
    {
        $this->attention = $attention;
    }

    /**
     * Devuelve el listado de atenciones.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllAttentions(): Collection
    {
        return $this->attention->all();
    }

    /**
     * Devuelve una atención por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function getAttentionById(int $id): ?Model
    {
        return $this->attention->find($id);
    }

    /**
     * Crea una nueva atención para un turno.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createAttention(array $data): Model
    {
        return $this->attention->create($data);
    }

    /**
     * Actualiza una atención para un turno.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updateAttention(int $id, array $data): bool
    {
        $attention = $this->attention->find($id);
        if (!$attention) {
            return false;
        }

        return $attention->update($data);
    }

    /**
     * Elimina una atención para un turno.
     * @param int $id
     * @return bool
    */
    public function deleteAttention(int $id): bool
    {
        $attention = $this->attention->find($id);
        if (!$attention) {
            return false;
        }

        return $attention->delete();
    }

    /**
     * Asigna patologías a una atención.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function attachPathologies(int $id, array $data): bool
    {
        $attention = $this->attention->find($id);
        if (!$attention) {
            return false;
        }

        $attention->pathologies()->attach($data);
        return true;
    }

    /**
     * Desasigna patologías de una atención.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function detachPathologies(int $id, array $data): bool
    {
        $attention = $this->attention->find($id);
        if (!$attention) {
            return false;
        }

        $attention->pathologies()->detach($data);
        return true;
    }
}
