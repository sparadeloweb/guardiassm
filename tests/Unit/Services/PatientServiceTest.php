<?php

use App\Services\PatientService;
use App\Repositories\Contracts\PatientRepositoryInterface;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

describe('PatientService', function () {

    test('getAllPatients devuelve una colección de pacientes', function () {
        // Arrange
        $repository = Mockery::mock(PatientRepositoryInterface::class);
        $service = new PatientService($repository);

        $patient1 = new Patient(['name' => 'Juan Pérez', 'DNI' => '12345678']);
        $patient1->id = 1;

        $patient2 = new Patient(['name' => 'María García', 'DNI' => '87654321']);
        $patient2->id = 2;

        $patients = new Collection([$patient1, $patient2]);

        $repository
            ->shouldReceive('getAllPatients')
            ->once()
            ->andReturn($patients);

        // Act
        $result = $service->getAllPatients();

        // Assert
        expect($result)->toBeInstanceOf(Collection::class)
            ->and($result->count())->toBe(2)
            ->and($result->first()->name)->toBe('Juan Pérez');

        Mockery::close();
    });

    test('getPatientById devuelve un paciente específico', function () {
        // Arrange
        $repository = Mockery::mock(PatientRepositoryInterface::class);
        $service = new PatientService($repository);

        $patient = new Patient([
            'name' => 'Juan Pérez',
            'DNI' => '12345678',
            'phone' => '1234567890',
            'address' => 'Calle Principal 123',
            'email' => 'juan@test.com',
            'gender' => 'male',
            'birth_date' => '1990-01-15'
        ]);
        $patient->id = 1;

        $repository
            ->shouldReceive('getPatientById')
            ->with(1)
            ->once()
            ->andReturn($patient);

        // Act
        $result = $service->getPatientById(1);

        // Assert
        expect($result)->toBeInstanceOf(Patient::class)
            ->and($result->id)->toBe(1)
            ->and($result->name)->toBe('Juan Pérez')
            ->and($result->DNI)->toBe('12345678');

        Mockery::close();
    });

    test('createPatient crea un nuevo paciente correctamente', function () {
        // Arrange
        $repository = Mockery::mock(PatientRepositoryInterface::class);
        $service = new PatientService($repository);

        $data = [
            'name' => 'Carlos López',
            'DNI' => '11223344',
            'phone' => '9876543210',
            'address' => 'Avenida Central 456',
            'email' => 'carlos@test.com',
            'gender' => 'male',
            'birth_date' => '1985-05-20'
        ];

        $patient = new Patient($data);
        $patient->id = 3;

        $repository
            ->shouldReceive('createPatient')
            ->with($data)
            ->once()
            ->andReturn($patient);

        // Act
        $result = $service->createPatient($data);

        // Assert
        expect($result)->toBeInstanceOf(Patient::class)
            ->and($result->id)->toBe(3)
            ->and($result->name)->toBe('Carlos López')
            ->and($result->DNI)->toBe('11223344');

        Mockery::close();
    });

    test('updatePatient actualiza un paciente existente', function () {
        // Arrange
        $repository = Mockery::mock(PatientRepositoryInterface::class);
        $service = new PatientService($repository);

        $id = 1;
        $data = [
            'name' => 'Juan Pérez Actualizado',
            'phone' => '111222333'
        ];

        $repository
            ->shouldReceive('updatePatient')
            ->with($id, $data)
            ->once()
            ->andReturn(true);

        // Act
        $result = $service->updatePatient($id, $data);

        // Assert
        expect($result)->toBeTrue();

        Mockery::close();
    });

    test('updatePatient devuelve false cuando falla la actualización', function () {
        // Arrange
        $repository = Mockery::mock(PatientRepositoryInterface::class);
        $service = new PatientService($repository);

        $id = 999;
        $data = ['name' => 'Test'];

        $repository
            ->shouldReceive('updatePatient')
            ->with($id, $data)
            ->once()
            ->andReturn(false);

        // Act
        $result = $service->updatePatient($id, $data);

        // Assert
        expect($result)->toBeFalse();

        Mockery::close();
    });

    test('deletePatient elimina un paciente correctamente', function () {
        // Arrange
        $repository = Mockery::mock(PatientRepositoryInterface::class);
        $service = new PatientService($repository);

        $id = 1;

        $repository
            ->shouldReceive('deletePatient')
            ->with($id)
            ->once()
            ->andReturn(true);

        // Act
        $result = $service->deletePatient($id);

        // Assert
        expect($result)->toBeTrue();

        Mockery::close();
    });

    test('deletePatient devuelve false cuando falla la eliminación', function () {
        // Arrange
        $repository = Mockery::mock(PatientRepositoryInterface::class);
        $service = new PatientService($repository);

        $id = 999;

        $repository
            ->shouldReceive('deletePatient')
            ->with($id)
            ->once()
            ->andReturn(false);

        // Act
        $result = $service->deletePatient($id);

        // Assert
        expect($result)->toBeFalse();

        Mockery::close();
    });

    test('el servicio delega correctamente todas las operaciones al repositorio', function () {
        // Arrange
        $repository = Mockery::mock(PatientRepositoryInterface::class);
        $service = new PatientService($repository);

        $collection = new Collection();
        $patient = new Patient(['name' => 'Test Patient']);
        $patient->id = 1;
        $data = ['name' => 'Test'];

        $repository->shouldReceive('getAllPatients')->once()->andReturn($collection);
        $repository->shouldReceive('getPatientById')->with(1)->once()->andReturn($patient);
        $repository->shouldReceive('createPatient')->with($data)->once()->andReturn($patient);
        $repository->shouldReceive('updatePatient')->with(1, $data)->once()->andReturn(true);
        $repository->shouldReceive('deletePatient')->with(1)->once()->andReturn(true);

        // Act & Assert
        expect($service->getAllPatients())->toBe($collection);
        expect($service->getPatientById(1))->toBe($patient);
        expect($service->createPatient($data))->toBe($patient);
        expect($service->updatePatient(1, $data))->toBeTrue();
        expect($service->deletePatient(1))->toBeTrue();

        Mockery::close();
    });
});

