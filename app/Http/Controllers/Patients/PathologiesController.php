<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use App\Services\Contracts\PathologyServiceInterface;
use App\Http\Requests\StorePathologyRequest;
use App\Http\Requests\UpdatePathologyRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PathologiesController extends Controller
{
    private $pathologyService;

    public function __construct(PathologyServiceInterface $pathologyService)
    {
        $this->pathologyService = $pathologyService;
    }

    /**
     * Muestra el listado de patologías.
     * @return \Inertia\Response
    */
    public function index(): Response
    {
        $pathologies = $this->pathologyService->getAllPathologies();

        return Inertia::render('Pathologies/Index', [
            'pathologies' => $pathologies
        ]);
    }

    /**
     * Muestra el formulario para crear una patología.
     * @return \Inertia\Response
    */
    public function create(): Response
    {
        return Inertia::render('Pathologies/Create');
    }

    /**
     * Crea una patología.
     * @param \App\Http\Requests\StorePathologyRequest $request
     * @return \Illuminate\Http\RedirectResponse
    */
    public function store(StorePathologyRequest $request): RedirectResponse
    {
        $this->pathologyService->createPathology($request->validated());

        return redirect()->route('pathologies.index')->with('success', 'Patología creada correctamente');
    }

    /**
     * Muestra una patología.
     * @param int $id
     * @return \Inertia\Response
    */
    public function show(int $id): Response
    {
        $pathology = $this->pathologyService->getPathologyById($id);

        return Inertia::render('Pathologies/Show', [
            'pathology' => $pathology
        ]);
    }

    /**
     * Muestra el formulario para editar una patología.
     * @param int $id
     * @return \Inertia\Response
    */
    public function edit(int $id): Response
    {
        $pathology = $this->pathologyService->getPathologyById($id);

        return Inertia::render('Pathologies/Edit', [
            'pathology' => $pathology
        ]);
    }

    /**
     * Actualiza una patología.
     * @param \App\Http\Requests\UpdatePathologyRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
    */
    public function update(UpdatePathologyRequest $request, int $id): RedirectResponse
    {
        $this->pathologyService->updatePathology($id, $request->validated());

        return redirect()->route('pathologies.show', $id)->with('success', 'Patología actualizada correctamente');
    }

    /**
     * Elimina una patología.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
    */
    public function destroy(int $id): RedirectResponse
    {
        $this->pathologyService->deletePathology($id);

        return redirect()->route('pathologies.index')->with('success', 'Patología eliminada correctamente');
    }
}
