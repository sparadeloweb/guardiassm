<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use App\Services\Contracts\PatientServiceInterface;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class PatientsController extends Controller
{
    private $patientService;

    public function __construct(PatientServiceInterface $patientService)
    {
        $this->patientService = $patientService;
    }

    /**
     * Muestra la lista de pacientes.
     * @return \Inertia\Response
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
     * @return \Inertia\Response
    */
    public function create(): Response
    {
        return Inertia::render('Patients/Create');
    }


    /**
     * Guarda un nuevo paciente.
     * @param StorePatientRequest $request
     * @return \Illuminate\Http\RedirectResponse
    */
    public function store(StorePatientRequest $request): RedirectResponse
    {
        $this->patientService->createPatient($request->validated());

        return redirect()->route('patients.index')->with('success', 'Paciente creado correctamente');
    }

    /**
     * Muestra un paciente específico.
     * @param string $id
     * @return \Inertia\Response
    */
    public function show(string $id): Response
    {
        return Inertia::render('Patients/Show', [
            'patient' => $this->patientService->getPatientById($id),
        ]);
    }

    /**
     * Muestra el formulario de edición de paciente.
     * @param string $id
     * @return \Inertia\Response
    */
    public function edit(string $id): Response
    {
        return Inertia::render('Patients/Edit', [
            'patient' => $this->patientService->getPatientById($id),
        ]);
    }

    /**
     * Actualiza un paciente.
     * @param UpdatePatientRequest $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
    */
    public function update(UpdatePatientRequest $request, string $id): RedirectResponse
    {
        $this->patientService->updatePatient($id, $request->validated());

        return redirect()->route('patients.show', $id)->with('success', 'Paciente actualizado correctamente');
    }

    /**
     * Elimina un paciente.
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
    */
    public function destroy(string $id): RedirectResponse
    {
        $this->patientService->deletePatient($id);

        return redirect()->route('patients.index')->with('success', 'Paciente eliminado correctamente');
    }
}
