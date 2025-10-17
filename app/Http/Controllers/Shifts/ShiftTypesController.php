<?php

namespace App\Http\Controllers\Shifts;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShiftTypeRequest;
use App\Http\Requests\UpdateShiftTypeRequest;
use App\Services\Contracts\ShiftTypeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ShiftTypesController extends Controller
{
    private $shiftTypeService;

    public function __construct(ShiftTypeServiceInterface $shiftTypeService)
    {
        $this->shiftTypeService = $shiftTypeService;
    }

    /**
     * Muestra la lista de tipos de turnos.
     */
    public function index(): Response
    {
        $shiftTypes = $this->shiftTypeService->getAllShiftTypes();

        return Inertia::render('ShiftTypes/Index', [
            'shiftTypes' => $shiftTypes,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo tipo de turno.
     */
    public function create(): Response
    {
        return Inertia::render('ShiftTypes/Create');
    }

    /**
     * Guarda un nuevo tipo de turno.
     */
    public function store(StoreShiftTypeRequest $request): RedirectResponse|JsonResponse
    {
        $shiftType = $this->shiftTypeService->createShiftType($request->validated());

        // Si es una petición API (desde drawer), retornar JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipo de turno creado correctamente',
                'data' => $shiftType,
            ], 201);
        }

        // Si es una petición normal, redireccionar al index
        return redirect()
            ->route('shift-types.index')
            ->with('success', 'Tipo de turno creado correctamente');
    }

    /**
     * Muestra un tipo de turno.
     */
    public function show(string $id): Response
    {
        $shiftType = $this->shiftTypeService->getShiftTypeById($id);

        return Inertia::render('ShiftTypes/Show', [
            'shiftType' => $shiftType,
        ]);
    }

    /**
     * Muestra el formulario para editar un tipo de turno.
     */
    public function edit(string $id): Response
    {
        $shiftType = $this->shiftTypeService->getShiftTypeById($id);

        return Inertia::render('ShiftTypes/Edit', [
            'shiftType' => $shiftType,
        ]);
    }

    /**
     * Actualiza un tipo de turno.
     */
    public function update(UpdateShiftTypeRequest $request, string $id): RedirectResponse
    {
        $this->shiftTypeService->updateShiftType($id, $request->validated());

        return redirect()->route('shift-types.show', $id)->with('success', 'Tipo de turno actualizado correctamente');
    }

    /**
     * Elimina un tipo de turno.
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->shiftTypeService->deleteShiftType($id);

        return redirect()->route('shift-types.index')->with('success', 'Tipo de turno eliminado correctamente');
    }
}
