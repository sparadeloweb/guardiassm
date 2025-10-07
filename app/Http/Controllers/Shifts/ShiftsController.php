<?php

namespace App\Http\Controllers\Shifts;

use App\Http\Controllers\Controller;
use App\Services\Contracts\ShiftServiceInterface;
use App\Services\Contracts\DoctorServiceInterface;
use App\Services\Contracts\ShiftTypeServiceInterface;
use App\Services\Contracts\PatientServiceInterface;
use App\Services\Contracts\PathologyServiceInterface;
use App\Http\Requests\StoreShiftRequest;
use App\Http\Requests\UpdateShiftRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;


class ShiftsController extends Controller
{
    private $shiftService;
    private $doctorService;
    private $shiftTypeService;
    private $patientService;
    private $pathologyService;

    public function __construct(
        ShiftServiceInterface $shiftService,
        DoctorServiceInterface $doctorService,
        ShiftTypeServiceInterface $shiftTypeService,
        PatientServiceInterface $patientService,
        PathologyServiceInterface $pathologyService
    ) {
        $this->shiftService = $shiftService;
        $this->doctorService = $doctorService;
        $this->shiftTypeService = $shiftTypeService;
        $this->patientService = $patientService;
        $this->pathologyService = $pathologyService;
    }

    /**
     * Muestra la lista de turnos.
     * @return \Inertia\Response
    */
    public function index(): Response
    {
        $shifts = $this->shiftService->getAllShifts();

        return Inertia::render('Shifts/Index', [
            'shifts' => $shifts
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo turno.
     * @return \Inertia\Response
    */
    public function create(): Response
    {
        $doctors = $this->doctorService->getAllDoctors();
        $shiftTypes = $this->shiftTypeService->getAllShiftTypes();
        $patients = $this->patientService->getAllPatients();
        $pathologies = $this->pathologyService->getAllPathologies();

        return Inertia::render('Shifts/Create', [
            'doctors' => $doctors,
            'shiftTypes' => $shiftTypes,
            'patients' => $patients,
            'pathologies' => $pathologies,
        ]);
    }

    /**
     * Guarda un nuevo turno.
     * @param StoreShiftRequest $request
     * @return \Illuminate\Http\RedirectResponse
    */
    public function store(StoreShiftRequest $request): RedirectResponse
    {
        try {
            $this->shiftService->createShift($request->validated());
            return redirect()->route('shifts.index')->with('success', 'Guardia creada correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Muestra un turno.
     * @param string $id
     * @return \Inertia\Response
    */
    public function show(string $id): Response
    {
        $shift = $this->shiftService->getShiftById($id);

        if (!$shift) {
            abort(404, 'Turno no encontrado');
        }

        return Inertia::render('Shifts/Show', [
            'shift' => $shift
        ]);
    }

    /**
     * Muestra el formulario para editar un turno.
     * @param string $id
     * @return \Inertia\Response
    */
    public function edit(string $id): Response
    {
        $shift = $this->shiftService->getShiftById($id);

        if (!$shift) {
            abort(404, 'Turno no encontrado');
        }

        return Inertia::render('Shifts/Edit', [
            'shift' => $shift,
            'doctors' => $this->doctorService->getAllDoctors(),
            'shiftTypes' => $this->shiftTypeService->getAllShiftTypes(),
            'patients' => $this->patientService->getAllPatients(),
            'pathologies' => $this->pathologyService->getAllPathologies(),
        ]);
    }

    /**
     * Actualiza un turno.
     * @param UpdateShiftRequest $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
    */
    public function update(UpdateShiftRequest $request, string $id): RedirectResponse
    {
        try {
            $this->shiftService->updateShift($id, $request->validated());
            return redirect()->route('shifts.show', $id)->with('success', 'Turno actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Elimina un turno.
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
    */
    public function destroy(string $id): RedirectResponse
    {
        $this->shiftService->deleteShift($id);

        return redirect()->route('shifts.index')->with('success', 'Turno eliminado correctamente');
    }
}
