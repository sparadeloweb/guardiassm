<?php

use App\Services\ShiftService;
use App\Repositories\Contracts\ShiftRepositoryInterface;
use App\Repositories\Contracts\ShiftTypeRepositoryInterface;
use App\Repositories\Contracts\AttentionRepositoryInterface;
use App\Models\Shift;
use App\Models\ShiftType;
use App\Models\Attention;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

test('getAllShifts devuelve todos los turnos', function () {
    $shiftRepository = Mockery::mock(ShiftRepositoryInterface::class);
    $shiftTypeRepository = Mockery::mock(ShiftTypeRepositoryInterface::class);
    $attentionRepository = Mockery::mock(AttentionRepositoryInterface::class);

    $service = new ShiftService($shiftRepository, $shiftTypeRepository, $attentionRepository);

    $shifts = new EloquentCollection([
        new Shift(['id' => 1, 'doctor_id' => 1, 'shift_type_id' => 1]),
        new Shift(['id' => 2, 'doctor_id' => 2, 'shift_type_id' => 2]),
    ]);

    $shiftRepository->shouldReceive('getAllShifts')->once()->andReturn($shifts);

    $result = $service->getAllShifts();

    expect($result)->toBe($shifts);
    Mockery::close();
});

test('getShiftById devuelve un turno específico', function () {
    $shiftRepository = Mockery::mock(ShiftRepositoryInterface::class);
    $shiftTypeRepository = Mockery::mock(ShiftTypeRepositoryInterface::class);
    $attentionRepository = Mockery::mock(AttentionRepositoryInterface::class);

    $service = new ShiftService($shiftRepository, $shiftTypeRepository, $attentionRepository);

    $shift = new Shift(['id' => 1, 'doctor_id' => 1, 'shift_type_id' => 1]);

    $shiftRepository->shouldReceive('getShiftById')->with(1)->once()->andReturn($shift);

    $result = $service->getShiftById(1);

    expect($result)->toBe($shift);
    Mockery::close();
});

test('createShift crea un turno con atenciones correctamente', function () {
    $shiftRepository = Mockery::mock(ShiftRepositoryInterface::class);
    $shiftTypeRepository = Mockery::mock(ShiftTypeRepositoryInterface::class);
    $attentionRepository = Mockery::mock(AttentionRepositoryInterface::class);

    // Mock DB::transaction
    DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
        return $callback();
    });

    $service = new ShiftService($shiftRepository, $shiftTypeRepository, $attentionRepository);

    $data = [
        'doctor_id' => 1,
        'shift_type_id' => 1,
        'starts_at' => '2025-10-07 08:00:00',
        'ends_at' => '2025-10-07 16:00:00',
        'notes' => 'Guardia clínica',
        'attentions' => [
            [
                'patient_id' => 1,
                'attended_at' => '2025-10-07 09:00:00',
                'notes' => 'Fiebre',
                'pathologies' => [1, 2],
                'per_patient_amount' => 100.00
            ]
        ]
    ];

    $shiftType = new ShiftType(['id' => 1, 'value' => 50.00, 'patient_value' => 25.00]);
    $shiftType->id = 1;

    $createdShift = new Shift($data);
    $createdShift->id = 1;

    $createdAttention = new Attention(['id' => 1, 'shift_id' => 1]);
    $createdAttention->id = 1;

    // Mock de validación de solapamiento (no hay turnos solapados)
    $shiftRepository->shouldReceive('getOverlappingShifts')
        ->with(1, '2025-10-07 08:00:00', '2025-10-07 16:00:00', null)
        ->once()
        ->andReturn(new EloquentCollection([]));

    // Mock de obtención del tipo de turno
    $shiftTypeRepository->shouldReceive('getShiftTypeById')->with(1)->once()->andReturn($shiftType);

    // Mock de creación del turno
    $shiftRepository->shouldReceive('createShift')->once()->andReturn($createdShift);

    // Mock de creación de atención
    $attentionRepository->shouldReceive('createAttention')->once()->andReturn($createdAttention);

    // Mock de attach de patologías
    $attentionRepository->shouldReceive('attachPathologies')->with(1, [1, 2])->once()->andReturn(true);

    $result = $service->createShift($data);

    expect($result)->toBe($createdShift);
    Mockery::close();
});

test('createShift asigna patología por defecto cuando no se especifican patologías', function () {
    $shiftRepository = Mockery::mock(ShiftRepositoryInterface::class);
    $shiftTypeRepository = Mockery::mock(ShiftTypeRepositoryInterface::class);
    $attentionRepository = Mockery::mock(AttentionRepositoryInterface::class);

    DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
        return $callback();
    });

    $service = new ShiftService($shiftRepository, $shiftTypeRepository, $attentionRepository);

    $data = [
        'doctor_id' => 1,
        'shift_type_id' => 1,
        'starts_at' => '2025-10-07 08:00:00',
        'ends_at' => '2025-10-07 16:00:00',
        'notes' => 'Guardia sin patologías específicas',
        'attentions' => [
            [
                'patient_id' => 1,
                'attended_at' => '2025-10-07 09:00:00',
                'notes' => 'Consulta general',
                // No se especifican patologías
            ]
        ]
    ];

    $shiftType = new ShiftType(['id' => 1, 'value' => 50.00, 'patient_value' => 25.00]);
    $shiftType->id = 1;

    $createdShift = new Shift($data);
    $createdShift->id = 1;

    $createdAttention = new Attention(['id' => 1, 'shift_id' => 1]);
    $createdAttention->id = 1;

    // Mock de validación de solapamiento (no hay turnos solapados)
    $shiftRepository->shouldReceive('getOverlappingShifts')
        ->with(1, '2025-10-07 08:00:00', '2025-10-07 16:00:00', null)
        ->once()
        ->andReturn(new EloquentCollection([]));

    // Mock de obtención del tipo de turno
    $shiftTypeRepository->shouldReceive('getShiftTypeById')->with(1)->once()->andReturn($shiftType);

    // Mock de creación del turno
    $shiftRepository->shouldReceive('createShift')->once()->andReturn($createdShift);

    // Mock de creación de atención
    $attentionRepository->shouldReceive('createAttention')->once()->andReturn($createdAttention);

    // Mock de attach de patología por defecto (ID 1)
    $attentionRepository->shouldReceive('attachPathologies')->with(1, [1])->once()->andReturn(true);

    $result = $service->createShift($data);

    expect($result)->toBe($createdShift);
    Mockery::close();
});

test('createShift lanza excepción cuando hay turnos solapados', function () {
    $shiftRepository = Mockery::mock(ShiftRepositoryInterface::class);
    $shiftTypeRepository = Mockery::mock(ShiftTypeRepositoryInterface::class);
    $attentionRepository = Mockery::mock(AttentionRepositoryInterface::class);

    // Mock DB::transaction
    DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
        return $callback();
    });

    $service = new ShiftService($shiftRepository, $shiftTypeRepository, $attentionRepository);

    $data = [
        'doctor_id' => 1,
        'shift_type_id' => 1,
        'starts_at' => '2025-10-07 08:00:00',
        'ends_at' => '2025-10-07 16:00:00',
        'notes' => 'Guardia clínica',
        'attentions' => []
    ];

    $overlappingShift = new Shift(['id' => 2, 'doctor_id' => 1]);
    $overlappingShift->id = 2;

    // Mock de validación de solapamiento (hay turnos solapados)
    $shiftRepository->shouldReceive('getOverlappingShifts')
        ->with(1, '2025-10-07 08:00:00', '2025-10-07 16:00:00', null)
        ->once()
        ->andReturn(new EloquentCollection([$overlappingShift]));

    // No debe llamar a getShiftTypeById ni createShift
    $shiftTypeRepository->shouldNotReceive('getShiftTypeById');
    $shiftRepository->shouldNotReceive('createShift');

    expect(fn() => $service->createShift($data))
        ->toThrow(\Exception::class, 'El doctor ya tiene un turno asignado en el horario especificado.');

    Mockery::close();
});

test('updateShift actualiza un turno con atenciones correctamente', function () {
    $shiftRepository = Mockery::mock(ShiftRepositoryInterface::class);
    $shiftTypeRepository = Mockery::mock(ShiftTypeRepositoryInterface::class);
    $attentionRepository = Mockery::mock(AttentionRepositoryInterface::class);

    // Mock DB::transaction
    DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
        return $callback();
    });

    $service = new ShiftService($shiftRepository, $shiftTypeRepository, $attentionRepository);

    $data = [
        'doctor_id' => 1,
        'shift_type_id' => 1,
        'starts_at' => '2025-10-07 08:00:00',
        'ends_at' => '2025-10-07 16:00:00',
        'notes' => 'Guardia clínica actualizada',
        'attentions' => [
            [
                'patient_id' => 2,
                'attended_at' => '2025-10-07 10:00:00',
                'notes' => 'Dolor de cabeza',
                'pathologies' => [3],
                'per_patient_amount' => 150.00
            ]
        ]
    ];

    $existingAttention = new Attention(['id' => 1, 'shift_id' => 1]);
    $existingAttention->id = 1;

    $existingShift = new Shift(['id' => 1, 'doctor_id' => 1, 'shift_type_id' => 1]);
    $existingShift->id = 1;
    $existingShift->attentions = collect([$existingAttention]);

    $shiftType = new ShiftType(['id' => 1, 'value' => 60.00, 'patient_value' => 30.00]);
    $shiftType->id = 1;

    $createdAttention = new Attention(['id' => 2, 'shift_id' => 1]);
    $createdAttention->id = 2;

    // Mock de obtención del turno existente
    $shiftRepository->shouldReceive('getShiftById')->with(1)->once()->andReturn($existingShift);

    // Mock de validación de solapamiento (excluyendo el turno actual)
    $shiftRepository->shouldReceive('getOverlappingShifts')
        ->with(1, '2025-10-07 08:00:00', '2025-10-07 16:00:00', 1)
        ->once()
        ->andReturn(new EloquentCollection([]));

    // Mock de obtención del tipo de turno
    $shiftTypeRepository->shouldReceive('getShiftTypeById')->with(1)->once()->andReturn($shiftType);

    // Mock de actualización del turno
    $shiftRepository->shouldReceive('updateShift')->with(1, Mockery::type('array'))->once()->andReturn(true);

    // Mock de eliminación de atenciones existentes
    $attentionRepository->shouldReceive('detachPathologies')->once()->andReturn(true);
    $attentionRepository->shouldReceive('deleteAttention')->once()->andReturn(true);

    // Mock de creación de nueva atención
    $attentionRepository->shouldReceive('createAttention')->once()->andReturn($createdAttention);

    // Mock de attach de patologías
    $attentionRepository->shouldReceive('attachPathologies')->with(2, [3])->once()->andReturn(true);

    $result = $service->updateShift(1, $data);

    expect($result)->toBeTrue();
    Mockery::close();
});

test('updateShift lanza excepción cuando hay turnos solapados', function () {
    $shiftRepository = Mockery::mock(ShiftRepositoryInterface::class);
    $shiftTypeRepository = Mockery::mock(ShiftTypeRepositoryInterface::class);
    $attentionRepository = Mockery::mock(AttentionRepositoryInterface::class);

    // Mock DB::transaction
    DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
        return $callback();
    });

    $service = new ShiftService($shiftRepository, $shiftTypeRepository, $attentionRepository);

    $data = [
        'doctor_id' => 1,
        'shift_type_id' => 1,
        'starts_at' => '2025-10-07 08:00:00',
        'ends_at' => '2025-10-07 16:00:00',
        'notes' => 'Guardia clínica',
        'attentions' => []
    ];

    $existingShift = new Shift(['id' => 1, 'doctor_id' => 1, 'shift_type_id' => 1]);
    $existingShift->id = 1;
    $existingShift->attentions = collect([]);

    $overlappingShift = new Shift(['id' => 3, 'doctor_id' => 1]);
    $overlappingShift->id = 3;

    // Mock de obtención del turno existente
    $shiftRepository->shouldReceive('getShiftById')->with(1)->once()->andReturn($existingShift);

    // Mock de validación de solapamiento (hay turnos solapados)
    $shiftRepository->shouldReceive('getOverlappingShifts')
        ->with(1, '2025-10-07 08:00:00', '2025-10-07 16:00:00', 1)
        ->once()
        ->andReturn(new EloquentCollection([$overlappingShift]));

    // No debe llamar a getShiftTypeById ni updateShift
    $shiftTypeRepository->shouldNotReceive('getShiftTypeById');
    $shiftRepository->shouldNotReceive('updateShift');

    expect(fn() => $service->updateShift(1, $data))
        ->toThrow(\Exception::class, 'El doctor ya tiene un turno asignado en el horario especificado.');

    Mockery::close();
});

test('deleteShift elimina un turno y sus atenciones correctamente', function () {
    $shiftRepository = Mockery::mock(ShiftRepositoryInterface::class);
    $shiftTypeRepository = Mockery::mock(ShiftTypeRepositoryInterface::class);
    $attentionRepository = Mockery::mock(AttentionRepositoryInterface::class);

    // Mock DB::transaction
    DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
        return $callback();
    });

    $service = new ShiftService($shiftRepository, $shiftTypeRepository, $attentionRepository);

    $existingAttention = new Attention(['id' => 1, 'shift_id' => 1]);
    $existingAttention->id = 1;

    $existingShift = new Shift(['id' => 1, 'doctor_id' => 1, 'shift_type_id' => 1]);
    $existingShift->id = 1;
    $existingShift->attentions = collect([$existingAttention]);

    // Mock de obtención del turno existente
    $shiftRepository->shouldReceive('getShiftById')->with(1)->once()->andReturn($existingShift);

    // Mock de eliminación de atenciones
    $attentionRepository->shouldReceive('detachPathologies')->with(1, [])->once()->andReturn(true);
    $attentionRepository->shouldReceive('deleteAttention')->with(1)->once()->andReturn(true);

    // Mock de eliminación del turno
    $shiftRepository->shouldReceive('deleteShift')->with(1)->once()->andReturn(true);

    $result = $service->deleteShift(1);

    expect($result)->toBeTrue();
    Mockery::close();
});

test('el servicio delega correctamente todas las operaciones al repositorio', function () {
    $shiftRepository = Mockery::mock(ShiftRepositoryInterface::class);
    $shiftTypeRepository = Mockery::mock(ShiftTypeRepositoryInterface::class);
    $attentionRepository = Mockery::mock(AttentionRepositoryInterface::class);

    // Mock DB::transaction para deleteShift
    DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
        return $callback();
    });

    $service = new ShiftService($shiftRepository, $shiftTypeRepository, $attentionRepository);

    $shift = new Shift(['id' => 1, 'doctor_id' => 1, 'shift_type_id' => 1]);
    $shift->id = 1;
    $shift->attentions = collect([]);

    $shiftRepository->shouldReceive('getAllShifts')->once()->andReturn(new EloquentCollection([$shift]));
    $shiftRepository->shouldReceive('getShiftById')->with(1)->twice()->andReturn($shift);
    $shiftRepository->shouldReceive('deleteShift')->with(1)->once()->andReturn(true);

    $service->getAllShifts();
    $service->getShiftById(1);
    $service->deleteShift(1);

    Mockery::close();
});
