<?php

use App\Services\ShiftTypeService;
use App\Repositories\Contracts\ShiftTypeRepositoryInterface;
use App\Models\ShiftType;
use Illuminate\Database\Eloquent\Collection;

describe('ShiftTypeService', function () {

    test('getAllShiftTypes devuelve una colección de tipos de turnos', function () {
        // Arrange
        $repository = Mockery::mock(ShiftTypeRepositoryInterface::class);
        $service = new ShiftTypeService($repository);

        $shiftType1 = new ShiftType(['name' => 'Guardia 12hs', 'description' => 'Turno de 12 horas', 'value' => 1500.00, 'patient_value' => 750.00]);
        $shiftType1->id = 1;

        $shiftType2 = new ShiftType(['name' => 'Guardia 24hs', 'description' => 'Turno de 24 horas', 'value' => 3000.00, 'patient_value' => 1500.00]);
        $shiftType2->id = 2;

        $shiftTypes = new Collection([$shiftType1, $shiftType2]);

        $repository
            ->shouldReceive('getAllShiftTypes')
            ->once()
            ->andReturn($shiftTypes);

        // Act
        $result = $service->getAllShiftTypes();

        // Assert
        expect($result)->toBeInstanceOf(Collection::class)
            ->and($result->count())->toBe(2)
            ->and($result->first()->name)->toBe('Guardia 12hs');

        Mockery::close();
    });

    test('getShiftTypeById devuelve un tipo de turno específico', function () {
        // Arrange
        $repository = Mockery::mock(ShiftTypeRepositoryInterface::class);
        $service = new ShiftTypeService($repository);

        $shiftType = new ShiftType([
            'name' => 'Guardia 12hs',
            'description' => 'Turno de 12 horas diurno',
            'value' => 1500.00,
            'patient_value' => 750.00
        ]);
        $shiftType->id = 1;

        $repository
            ->shouldReceive('getShiftTypeById')
            ->with(1)
            ->once()
            ->andReturn($shiftType);

        // Act
        $result = $service->getShiftTypeById(1);

        // Assert
        expect($result)->toBeInstanceOf(ShiftType::class)
            ->and($result->id)->toBe(1)
            ->and($result->name)->toBe('Guardia 12hs')
            ->and($result->description)->toBe('Turno de 12 horas diurno')
            ->and($result->value)->toBe(1500.00)
            ->and($result->patient_value)->toBe(750.00);

        Mockery::close();
    });

    test('createShiftType crea un nuevo tipo de turno correctamente', function () {
        // Arrange
        $repository = Mockery::mock(ShiftTypeRepositoryInterface::class);
        $service = new ShiftTypeService($repository);

        $data = [
            'name' => 'Guardia 24hs',
            'description' => 'Turno de 24 horas completo',
            'value' => 3000.00,
            'patient_value' => 1500.00
        ];

        $shiftType = new ShiftType($data);
        $shiftType->id = 3;

        $repository
            ->shouldReceive('createShiftType')
            ->with($data)
            ->once()
            ->andReturn($shiftType);

        // Act
        $result = $service->createShiftType($data);

        // Assert
        expect($result)->toBeInstanceOf(ShiftType::class)
            ->and($result->id)->toBe(3)
            ->and($result->name)->toBe('Guardia 24hs')
            ->and($result->description)->toBe('Turno de 24 horas completo')
            ->and($result->value)->toBe(3000.00)
            ->and($result->patient_value)->toBe(1500.00);

        Mockery::close();
    });

    test('updateShiftType actualiza un tipo de turno existente', function () {
        // Arrange
        $repository = Mockery::mock(ShiftTypeRepositoryInterface::class);
        $service = new ShiftTypeService($repository);

        $id = 1;
        $data = [
            'name' => 'Guardia 12hs Actualizada',
            'value' => 1800.00,
            'patient_value' => 900.00
        ];

        $repository
            ->shouldReceive('updateShiftType')
            ->with($id, $data)
            ->once()
            ->andReturn(true);

        // Act
        $result = $service->updateShiftType($id, $data);

        // Assert
        expect($result)->toBeTrue();

        Mockery::close();
    });

    test('updateShiftType devuelve false cuando falla la actualización', function () {
        // Arrange
        $repository = Mockery::mock(ShiftTypeRepositoryInterface::class);
        $service = new ShiftTypeService($repository);

        $id = 999;
        $data = ['name' => 'Test'];

        $repository
            ->shouldReceive('updateShiftType')
            ->with($id, $data)
            ->once()
            ->andReturn(false);

        // Act
        $result = $service->updateShiftType($id, $data);

        // Assert
        expect($result)->toBeFalse();

        Mockery::close();
    });

    test('deleteShiftType elimina un tipo de turno correctamente', function () {
        // Arrange
        $repository = Mockery::mock(ShiftTypeRepositoryInterface::class);
        $service = new ShiftTypeService($repository);

        $id = 1;

        $repository
            ->shouldReceive('deleteShiftType')
            ->with($id)
            ->once()
            ->andReturn(true);

        // Act
        $result = $service->deleteShiftType($id);

        // Assert
        expect($result)->toBeTrue();

        Mockery::close();
    });

    test('deleteShiftType devuelve false cuando falla la eliminación', function () {
        // Arrange
        $repository = Mockery::mock(ShiftTypeRepositoryInterface::class);
        $service = new ShiftTypeService($repository);

        $id = 999;

        $repository
            ->shouldReceive('deleteShiftType')
            ->with($id)
            ->once()
            ->andReturn(false);

        // Act
        $result = $service->deleteShiftType($id);

        // Assert
        expect($result)->toBeFalse();

        Mockery::close();
    });

    test('el servicio delega correctamente todas las operaciones al repositorio', function () {
        // Arrange
        $repository = Mockery::mock(ShiftTypeRepositoryInterface::class);
        $service = new ShiftTypeService($repository);

        $collection = new Collection();
        $shiftType = new ShiftType(['name' => 'Test Shift', 'value' => 1000.00, 'patient_value' => 500.00]);
        $shiftType->id = 1;
        $data = ['name' => 'Test', 'value' => 1000.00, 'patient_value' => 500.00];

        $repository->shouldReceive('getAllShiftTypes')->once()->andReturn($collection);
        $repository->shouldReceive('getShiftTypeById')->with(1)->once()->andReturn($shiftType);
        $repository->shouldReceive('createShiftType')->with($data)->once()->andReturn($shiftType);
        $repository->shouldReceive('updateShiftType')->with(1, $data)->once()->andReturn(true);
        $repository->shouldReceive('deleteShiftType')->with(1)->once()->andReturn(true);

        // Act & Assert
        expect($service->getAllShiftTypes())->toBe($collection);
        expect($service->getShiftTypeById(1))->toBe($shiftType);
        expect($service->createShiftType($data))->toBe($shiftType);
        expect($service->updateShiftType(1, $data))->toBeTrue();
        expect($service->deleteShiftType(1))->toBeTrue();

        Mockery::close();
    });
});


