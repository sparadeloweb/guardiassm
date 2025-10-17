<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Services\Contracts\PatientServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PatientsController extends Controller
{
    private $patientService;

    public function __construct(PatientServiceInterface $patientService)
    {
        $this->patientService = $patientService;
    }

    /**
     * Muestra la lista de pacientes.
     */
    public function index(): Response
    {
        $patients = $this->patientService->getAllPatients();

        return Inertia::render('Patients/Index', [
            'patients' => $patients,
        ]);
    }

    /**
     * Muestra el formulario de creación de paciente.
     */
    public function create(): Response
    {
        return Inertia::render('Patients/Create');
    }

    /**
     * Guarda un nuevo paciente.
     */
    public function store(StorePatientRequest $request): RedirectResponse|JsonResponse
    {
        $patient = $this->patientService->createPatient($request->validated());

        // Si es una petición API (desde drawer), retornar JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Paciente creado correctamente',
                'data' => $patient,
            ], 201);
        }

        // Si es una petición normal, redireccionar al index
        return redirect()
            ->route('patients.index')
            ->with('success', 'Paciente creado correctamente');
    }

    /**
     * Muestra un paciente específico.
     */
    public function show(string $id): Response
    {
        return Inertia::render('Patients/Show', [
            'patient' => $this->patientService->getPatientById($id),
        ]);
    }

    /**
     * Muestra el formulario de edición de paciente.
     */
    public function edit(string $id): Response
    {
        return Inertia::render('Patients/Edit', [
            'patient' => $this->patientService->getPatientById($id),
        ]);
    }

    /**
     * Actualiza un paciente.
     */
    public function update(UpdatePatientRequest $request, string $id): RedirectResponse
    {
        $this->patientService->updatePatient($id, $request->validated());

        return redirect()->route('patients.show', $id)->with('success', 'Paciente actualizado correctamente');
    }

    /**
     * Elimina un paciente.
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->patientService->deletePatient($id);

        return redirect()->route('patients.index')->with('success', 'Paciente eliminado correctamente');
    }
}
