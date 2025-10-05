<?php

namespace App\Http\Controllers\Doctors;

use App\Http\Controllers\Controller;
use App\Services\Contracts\DoctorServiceInterface;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class DoctorsController extends Controller
{
    private $doctorService;

    public function __construct(DoctorServiceInterface $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    /**
     * Muestra la lista de doctores.
     * @return \Inertia\Response
    */
    public function index(): Response
    {
        $doctors = $this->doctorService->getAllDoctors();

        return Inertia::render('Doctors/Index', [
            'doctors' => $doctors
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo doctor.
     * @return \Inertia\Response
    */
    public function create(): Response
    {
        return Inertia::render('Doctors/Create');
    }

    /**
     * Guarda un nuevo doctor.
     * @param StoreDoctorRequest $request
     * @return \Illuminate\Http\RedirectResponse
    */
    public function store(StoreDoctorRequest $request): RedirectResponse
    {
        $this->doctorService->createDoctor($request->validated());

        return redirect()->route('doctors.index')->with('success', 'Doctor creado correctamente');
    }

    /**
     * Muestra un doctor específico.
     * @param string $id
     * @return \Inertia\Response
    */
    public function show(string $id): Response
    {
        $doctor = $this->doctorService->getDoctorById($id);

        return Inertia::render('Doctors/Show', [
            'doctor' => $doctor
        ]);
    }

    /**
     * Muestra el formulario para editar un doctor.
     * @param string $id
     * @return \Inertia\Response
    */
    public function edit(string $id): Response
    {
        $doctor = $this->doctorService->getDoctorById($id);

        return Inertia::render('Doctors/Edit', [
            'doctor' => $doctor
        ]);
    }

    /**
     * Actualiza un doctor.
     * @param UpdateDoctorRequest $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
    */
    public function update(UpdateDoctorRequest $request, string $id): RedirectResponse
    {
        $this->doctorService->updateDoctor($id, $request->validated());

        return redirect()->route('doctors.show', $id)->with('success', 'Doctor actualizado correctamente');
    }

    /**
     * Elimina un doctor.
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
    */
    public function destroy(string $id): RedirectResponse
    {
        $this->doctorService->deleteDoctor($id);

        return redirect()->route('doctors.index')->with('success', 'Doctor eliminado correctamente');
    }
}
