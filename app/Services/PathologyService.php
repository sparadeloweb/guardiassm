<?php

namespace App\Services;

use App\Services\Contracts\PathologyServiceInterface;
use App\Repositories\Contracts\PathologyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PathologyService implements PathologyServiceInterface
{
    private $pathologyRepository;

    public function __construct(PathologyRepositoryInterface $pathologyRepository)
    {
        $this->pathologyRepository = $pathologyRepository;
    }

    /**
     * Verifica si una patología está protegida (ID = 1).
     * @param int $id
     * @return bool
     */
    private function isProtectedPathology(int $id): bool
    {
        return $id === 1;
    }

    /**
     * Verifica si una patología puede ser editada.
     * @param int $id
     * @throws \Exception Si la patología está protegida
     */
    private function checkCanEdit(int $id): void
    {
        if ($this->isProtectedPathology($id)) {
            throw new \Exception('Esta patología no se puede editar');
        }
    }

    /**
     * Verifica si una patología puede ser eliminada.
     * @param int $id
     * @throws \Exception Si la patología está protegida
    */
    private function checkCanDelete(int $id): void
    {
        if ($this->isProtectedPathology($id)) {
            throw new \Exception('Esta patología no se puede eliminar');
        }
    }

    /**
     * Devuelve el listado de patologías.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllPathologies(): Collection
    {
        return $this->pathologyRepository->getAllPathologies();
    }

    /**
     * Devuelve una patología por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function getPathologyById(int $id): Model
    {
        return $this->pathologyRepository->getPathologyById($id);
    }

    /**
     * Crea una nueva patología.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createPathology(array $data): Model
    {
        return $this->pathologyRepository->createPathology($data);
    }

    /**
     * Actualiza una patología.
     * @param int $id
     * @param array $data
     * @return bool
     * @throws \Exception Si la patología está protegida
    */
    public function updatePathology(int $id, array $data): bool
    {
        $this->checkCanEdit($id);
        return $this->pathologyRepository->updatePathology($id, $data);
    }

    /**
     * Elimina una patología.
     * @param int $id
     * @return bool
     * @throws \Exception Si la patología está protegida
    */
    public function deletePathology(int $id): bool
    {
        $this->checkCanDelete($id);
        return $this->pathologyRepository->deletePathology($id);
    }
}
