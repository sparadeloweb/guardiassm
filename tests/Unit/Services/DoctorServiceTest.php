<?php

use App\Services\DoctorService;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Collection;

describe('DoctorService', function () {

    test('getAllDoctors devuelve una colección de doctores', function () {
        // Arrange
        $repository = Mockery::mock(DoctorRepositoryInterface::class);
        $service = new DoctorService($repository);

        $doctor1 = new Doctor(['name' => 'Dr. Juan Pérez', 'email' => 'juan@test.com']);
        $doctor1->id = 1;

        $doctor2 = new Doctor(['name' => 'Dra. María García', 'email' => 'maria@test.com']);
        $doctor2->id = 2;

        $doctors = new Collection([$doctor1, $doctor2]);

        $repository
            ->shouldReceive('getAllDoctors')
            ->once()
            ->andReturn($doctors);

        // Act
        $result = $service->getAllDoctors();

        // Assert
        expect($result)->toBeInstanceOf(Collection::class)
            ->and($result->count())->toBe(2)
            ->and($result->first()->name)->toBe('Dr. Juan Pérez');

        Mockery::close();
    });

    test('getDoctorById devuelve un doctor específico', function () {
        // Arrange
        $repository = Mockery::mock(DoctorRepositoryInterface::class);
        $service = new DoctorService($repository);

        $doctor = new Doctor([
            'name' => 'Dr. Juan Pérez',
            'email' => 'juan@test.com',
            'age' => 45,
            'phone' => '123456789',
            'address' => 'Calle Principal 123'
        ]);
        $doctor->id = 1;

        $repository
            ->shouldReceive('getDoctorById')
            ->with(1)
            ->once()
            ->andReturn($doctor);

        // Act
        $result = $service->getDoctorById(1);

        // Assert
        expect($result)->toBeInstanceOf(Doctor::class)
            ->and($result->id)->toBe(1)
            ->and($result->name)->toBe('Dr. Juan Pérez')
            ->and($result->email)->toBe('juan@test.com');

        Mockery::close();
    });

    test('createDoctor crea un nuevo doctor correctamente', function () {
        // Arrange
        $repository = Mockery::mock(DoctorRepositoryInterface::class);
        $service = new DoctorService($repository);

        $data = [
            'name' => 'Dr. Carlos López',
            'email' => 'carlos@test.com',
            'age' => 38,
            'phone' => '987654321',
            'address' => 'Avenida Central 456'
        ];

        $doctor = new Doctor($data);
        $doctor->id = 3;

        $repository
            ->shouldReceive('createDoctor')
            ->with($data)
            ->once()
            ->andReturn($doctor);

        // Act
        $result = $service->createDoctor($data);

        // Assert
        expect($result)->toBeInstanceOf(Doctor::class)
            ->and($result->id)->toBe(3)
            ->and($result->name)->toBe('Dr. Carlos López')
            ->and($result->email)->toBe('carlos@test.com');

        Mockery::close();
    });

    test('updateDoctor actualiza un doctor existente', function () {
        // Arrange
        $repository = Mockery::mock(DoctorRepositoryInterface::class);
        $service = new DoctorService($repository);

        $id = 1;
        $data = [
            'name' => 'Dr. Juan Pérez Actualizado',
            'phone' => '111222333'
        ];

        $repository
            ->shouldReceive('updateDoctor')
            ->with($id, $data)
            ->once()
            ->andReturn(true);

        // Act
        $result = $service->updateDoctor($id, $data);

        // Assert
        expect($result)->toBeTrue();

        Mockery::close();
    });

    test('updateDoctor devuelve false cuando falla la actualización', function () {
        // Arrange
        $repository = Mockery::mock(DoctorRepositoryInterface::class);
        $service = new DoctorService($repository);

        $id = 999;
        $data = ['name' => 'Test'];

        $repository
            ->shouldReceive('updateDoctor')
            ->with($id, $data)
            ->once()
            ->andReturn(false);

        // Act
        $result = $service->updateDoctor($id, $data);

        // Assert
        expect($result)->toBeFalse();

        Mockery::close();
    });

    test('deleteDoctor elimina un doctor correctamente', function () {
        // Arrange
        $repository = Mockery::mock(DoctorRepositoryInterface::class);
        $service = new DoctorService($repository);

        $id = 1;

        $repository
            ->shouldReceive('deleteDoctor')
            ->with($id)
            ->once()
            ->andReturn(true);

        // Act
        $result = $service->deleteDoctor($id);

        // Assert
        expect($result)->toBeTrue();

        Mockery::close();
    });

    test('deleteDoctor devuelve false cuando falla la eliminación', function () {
        // Arrange
        $repository = Mockery::mock(DoctorRepositoryInterface::class);
        $service = new DoctorService($repository);

        $id = 999;

        $repository
            ->shouldReceive('deleteDoctor')
            ->with($id)
            ->once()
            ->andReturn(false);

        // Act
        $result = $service->deleteDoctor($id);

        // Assert
        expect($result)->toBeFalse();

        Mockery::close();
    });

    test('el servicio delega correctamente todas las operaciones al repositorio', function () {
        // Arrange
        $repository = Mockery::mock(DoctorRepositoryInterface::class);
        $service = new DoctorService($repository);

        $collection = new Collection();
        $doctor = new Doctor(['name' => 'Test Doctor']);
        $doctor->id = 1;
        $data = ['name' => 'Test'];

        $repository->shouldReceive('getAllDoctors')->once()->andReturn($collection);
        $repository->shouldReceive('getDoctorById')->with(1)->once()->andReturn($doctor);
        $repository->shouldReceive('createDoctor')->with($data)->once()->andReturn($doctor);
        $repository->shouldReceive('updateDoctor')->with(1, $data)->once()->andReturn(true);
        $repository->shouldReceive('deleteDoctor')->with(1)->once()->andReturn(true);

        // Act & Assert
        expect($service->getAllDoctors())->toBe($collection);
        expect($service->getDoctorById(1))->toBe($doctor);
        expect($service->createDoctor($data))->toBe($doctor);
        expect($service->updateDoctor(1, $data))->toBeTrue();
        expect($service->deleteDoctor(1))->toBeTrue();

        Mockery::close();
    });
});

