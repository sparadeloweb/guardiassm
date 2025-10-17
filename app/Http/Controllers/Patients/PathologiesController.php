<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePathologyRequest;
use App\Http\Requests\UpdatePathologyRequest;
use App\Services\Contracts\PathologyServiceInterface;
use Illuminate\Http\JsonResponse;
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
     */
    public function index(): Response
    {
        $pathologies = $this->pathologyService->getAllPathologies();

        return Inertia::render('Pathologies/Index', [
            'pathologies' => $pathologies,
        ]);
    }

    /**
     * Muestra el formulario para crear una patología.
     */
    public function create(): Response
    {
        return Inertia::render('Pathologies/Create');
    }

    /**
     * Crea una patología.
     */
    public function store(StorePathologyRequest $request): RedirectResponse|JsonResponse
    {
        $pathology = $this->pathologyService->createPathology($request->validated());

        // Si es una petición API (desde drawer), retornar JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Patología creada correctamente',
                'data' => $pathology,
            ], 201);
        }

        // Si es una petición normal, redireccionar al index
        return redirect()
            ->route('pathologies.index')
            ->with('success', 'Patología creada correctamente');
    }

    /**
     * Muestra una patología.
     */
    public function show(int $id): Response
    {
        $pathology = $this->pathologyService->getPathologyById($id);

        return Inertia::render('Pathologies/Show', [
            'pathology' => $pathology,
        ]);
    }

    /**
     * Muestra el formulario para editar una patología.
     */
    public function edit(int $id): Response
    {
        $pathology = $this->pathologyService->getPathologyById($id);

        return Inertia::render('Pathologies/Edit', [
            'pathology' => $pathology,
        ]);
    }

    /**
     * Actualiza una patología.
     */
    public function update(UpdatePathologyRequest $request, int $id): RedirectResponse
    {
        $this->pathologyService->updatePathology($id, $request->validated());

        return redirect()->route('pathologies.show', $id)->with('success', 'Patología actualizada correctamente');
    }

    /**
     * Elimina una patología.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->pathologyService->deletePathology($id);

        return redirect()->route('pathologies.index')->with('success', 'Patología eliminada correctamente');
    }
}
