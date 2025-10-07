<?php

namespace App\Services;

use App\Services\Contracts\ShiftServiceInterface;
use App\Repositories\Contracts\ShiftRepositoryInterface;
use App\Repositories\Contracts\ShiftTypeRepositoryInterface;
use App\Repositories\Contracts\AttentionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShiftService implements ShiftServiceInterface
{
    private $shiftRepository;
    private $shiftTypeRepository;
    private $attentionRepository;

    public function __construct(
        ShiftRepositoryInterface $shiftRepository,
        ShiftTypeRepositoryInterface $shiftTypeRepository,
        AttentionRepositoryInterface $attentionRepository
    ) {
        $this->shiftRepository = $shiftRepository;
        $this->shiftTypeRepository = $shiftTypeRepository;
        $this->attentionRepository = $attentionRepository;
    }

    /**
     * Devuelve el listado de turnos.
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function getAllShifts(): Collection
    {
        return $this->shiftRepository->getAllShifts();
    }

    /**
     * Devuelve un turno por su ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function getShiftById(int $id): ?Model
    {
        return $this->shiftRepository->getShiftById($id);
    }

    /**
     * Crea un nuevo turno con sus atenciones.
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
    */
    public function createShift(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // Validar que el doctor no tenga turnos solapados
            $this->validateNoOverlappingShifts($data['doctor_id'], $data['starts_at'], $data['ends_at']);

            // Obtener snapshots de las tarifas del tipo de turno
            $shiftType = $this->shiftTypeRepository->getShiftTypeById($data['shift_type_id']);
            $hourlyRateSnapshot = $shiftType->value;
            $perPatientRateSnapshot = $shiftType->patient_value;

            // Calcular total_hours
            $totalHours = $this->calculateTotalHours($data['starts_at'], $data['ends_at']);

            // Preparar datos del turno
            $shiftData = [
                'doctor_id' => $data['doctor_id'],
                'shift_type_id' => $data['shift_type_id'],
                'hourly_rate_snapshot' => $hourlyRateSnapshot,
                'per_patient_rate_snapshot' => $perPatientRateSnapshot,
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'total_hours' => $totalHours,
                'patients_count' => count($data['attentions'] ?? []),
                'notes' => $data['notes'] ?? null,
            ];

            // Calcular total_amount basado en horas y pacientes
            $hourlyAmount = $totalHours * $hourlyRateSnapshot;
            $patientAmount = $this->calculatePatientAmount($data['attentions'] ?? [], $perPatientRateSnapshot);
            $shiftData['total_amount'] = $hourlyAmount + $patientAmount;

            // Crear el turno
            $shift = $this->shiftRepository->createShift($shiftData);

            // Crear las atenciones si existen
            if (isset($data['attentions']) && is_array($data['attentions'])) {
                $this->createAttentions($shift->id, $data['attentions']);
            }

            return $shift;
        });
    }

    /**
     * Actualiza un turno existente con sus atenciones.
     * @param int $id
     * @param array $data
     * @return bool
    */
    public function updateShift(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $shift = $this->shiftRepository->getShiftById($id);

            // Validar que el doctor no tenga turnos solapados (excluyendo el actual)
            $this->validateNoOverlappingShifts($data['doctor_id'], $data['starts_at'], $data['ends_at'], $id);

            // Obtener snapshots de las tarifas del tipo de turno
            $shiftType = $this->shiftTypeRepository->getShiftTypeById($data['shift_type_id']);
            $hourlyRateSnapshot = $shiftType->value;
            $perPatientRateSnapshot = $shiftType->patient_value;

            // Calcular total_hours
            $totalHours = $this->calculateTotalHours($data['starts_at'], $data['ends_at']);

            // Preparar datos del turno
            $shiftData = [
                'doctor_id' => $data['doctor_id'],
                'shift_type_id' => $data['shift_type_id'],
                'hourly_rate_snapshot' => $hourlyRateSnapshot,
                'per_patient_rate_snapshot' => $perPatientRateSnapshot,
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'total_hours' => $totalHours,
                'patients_count' => count($data['attentions'] ?? []),
                'notes' => $data['notes'] ?? null,
            ];

            // Calcular total_amount basado en horas y pacientes
            $hourlyAmount = $totalHours * $hourlyRateSnapshot;
            $patientAmount = $this->calculatePatientAmount($data['attentions'] ?? [], $perPatientRateSnapshot);
            $shiftData['total_amount'] = $hourlyAmount + $patientAmount;

            // Actualizar el turno
            $updated = $this->shiftRepository->updateShift($id, $shiftData);

            if ($updated) {
                // Eliminar atenciones existentes y crear las nuevas
                $this->deleteExistingAttentions($shift);

                // Crear las nuevas atenciones si existen
                if (isset($data['attentions']) && is_array($data['attentions'])) {
                    $this->createAttentions($id, $data['attentions']);
                }
            }

            return $updated;
        });
    }

    /**
     * Elimina un turno y todas sus atenciones.
     * @param int $id
     * @return bool
    */
    public function deleteShift(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $shift = $this->shiftRepository->getShiftById($id);

            // Eliminar todas las atenciones del turno
            $this->deleteExistingAttentions($shift);

            // Eliminar el turno
            return $this->shiftRepository->deleteShift($id);
        });
    }

    /**
     * Valida que el doctor no tenga turnos solapados.
     * @param int $doctorId
     * @param string $startsAt
     * @param string $endsAt
     * @param int|null $excludeShiftId
     * @throws \Exception Si hay turnos solapados
    */
    private function validateNoOverlappingShifts(int $doctorId, string $startsAt, string $endsAt, ?int $excludeShiftId = null): void
    {
        $overlappingShifts = $this->shiftRepository->getOverlappingShifts($doctorId, $startsAt, $endsAt, $excludeShiftId);

        if ($overlappingShifts->count() > 0) {
            throw new \Exception('El doctor ya tiene un turno asignado en el horario especificado.');
        }
    }

    /**
     * Calcula las horas totales entre dos fechas.
     * @param string $startsAt
     * @param string $endsAt
     * @return float
    */
    private function calculateTotalHours(string $startsAt, string $endsAt): float
    {
        $start = \Carbon\Carbon::parse($startsAt);
        $end = \Carbon\Carbon::parse($endsAt);

        return round($start->diffInMinutes($end) / 60, 2);
    }

    /**
     * Calcula el monto total de pacientes.
     * @param array $attentions
     * @param float $perPatientRateSnapshot
     * @return float
    */
    private function calculatePatientAmount(array $attentions, float $perPatientRateSnapshot): float
    {
        $totalAmount = 0;

        foreach ($attentions as $attention) {
            // Si se especifica un monto personalizado, usarlo; sino usar el snapshot
            $amount = $attention['per_patient_amount'] ?? $perPatientRateSnapshot;
            $totalAmount += $amount;
        }

        return $totalAmount;
    }

    /**
     * Crea las atenciones para un turno.
     * @param int $shiftId
     * @param array $attentions
    */
    private function createAttentions(int $shiftId, array $attentions): void
    {
        foreach ($attentions as $attentionData) {
            $attention = $this->attentionRepository->createAttention([
                'shift_id' => $shiftId,
                'patient_id' => $attentionData['patient_id'],
                'attended_at' => $attentionData['attended_at'],
                'notes' => $attentionData['notes'] ?? null,
                'per_patient_amount' => $attentionData['per_patient_amount'] ?? null,
            ]);

            // Attachear patologías si existen, o asignar patología por defecto (ID 1)
            if (isset($attentionData['pathologies']) && is_array($attentionData['pathologies']) && !empty($attentionData['pathologies'])) {
                $this->attentionRepository->attachPathologies($attention->id, $attentionData['pathologies']);
            } else {
                // Asignar patología por defecto (ID 1 - "Patología no especificada")
                $this->attentionRepository->attachPathologies($attention->id, [1]);
            }
        }
    }

    /**
     * Elimina todas las atenciones existentes de un turno.
     * @param Model $shift
    */
    private function deleteExistingAttentions(Model $shift): void
    {
        foreach ($shift->attentions as $attention) {
            // Desattachear patologías primero
            $this->attentionRepository->detachPathologies($attention->id, []);

            // Eliminar la atención
            $this->attentionRepository->deleteAttention($attention->id);
        }
    }
}
