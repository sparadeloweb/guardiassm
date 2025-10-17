<?php

namespace App\Http\Controllers\Doctors;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Services\Contracts\DoctorServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DoctorsController extends Controller
{
    private $doctorService;

    public function __construct(DoctorServiceInterface $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    /**
     * Muestra la lista de doctores.
     */
    public function index(): Response
    {
        $doctors = $this->doctorService->getAllDoctors();

        return Inertia::render('Doctors/Index', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo doctor.
     */
    public function create(): Response
    {
        return Inertia::render('Doctors/Create');
    }

    /**
     * Guarda un nuevo doctor.
     */
    public function store(StoreDoctorRequest $request): RedirectResponse|JsonResponse
    {
        $doctor = $this->doctorService->createDoctor($request->validated());

        // Si es una petición API (desde drawer), retornar JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Doctor creado correctamente',
                'data' => $doctor,
            ], 201);
        }

        // Si es una petición normal, redireccionar al index
        return redirect()
            ->route('doctors.index')
            ->with('success', 'Doctor creado correctamente');
    }

    /**
     * Muestra un doctor específico.
     */
    public function show(string $id): Response
    {
        $doctor = $this->doctorService->getDoctorById($id);

        return Inertia::render('Doctors/Show', [
            'doctor' => $doctor,
        ]);
    }

    /**
     * Muestra el formulario para editar un doctor.
     */
    public function edit(string $id): Response
    {
        $doctor = $this->doctorService->getDoctorById($id);

        return Inertia::render('Doctors/Edit', [
            'doctor' => $doctor,
        ]);
    }

    /**
     * Actualiza un doctor.
     */
    public function update(UpdateDoctorRequest $request, string $id): RedirectResponse
    {
        $this->doctorService->updateDoctor($id, $request->validated());

        return redirect()->route('doctors.show', $id)->with('success', 'Doctor actualizado correctamente');
    }

    /**
     * Elimina un doctor.
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->doctorService->deleteDoctor($id);

        return redirect()->route('doctors.index')->with('success', 'Doctor eliminado correctamente');
    }
}
