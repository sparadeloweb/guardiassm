<?php

use App\Services\PathologyService;
use App\Repositories\Contracts\PathologyRepositoryInterface;
use App\Models\Pathology;
use Illuminate\Database\Eloquent\Collection;

describe('PathologyService', function () {

    test('getAllPathologies devuelve una colección de patologías', function () {
        // Arrange
        $repository = Mockery::mock(PathologyRepositoryInterface::class);
        $service = new PathologyService($repository);

        $pathology1 = new Pathology(['name' => 'Diabetes', 'description' => 'Enfermedad metabólica']);
        $pathology1->id = 1;

        $pathology2 = new Pathology(['name' => 'Hipertensión', 'description' => 'Presión arterial alta']);
        $pathology2->id = 2;

        $pathologies = new Collection([$pathology1, $pathology2]);

        $repository
            ->shouldReceive('getAllPathologies')
            ->once()
            ->andReturn($pathologies);

        // Act
        $result = $service->getAllPathologies();

        // Assert
        expect($result)->toBeInstanceOf(Collection::class)
            ->and($result->count())->toBe(2)
            ->and($result->first()->name)->toBe('Diabetes');

        Mockery::close();
    });

    test('getPathologyById devuelve una patología específica', function () {
        // Arrange
        $repository = Mockery::mock(PathologyRepositoryInterface::class);
        $service = new PathologyService($repository);

        $pathology = new Pathology([
            'name' => 'Asma',
            'description' => 'Enfermedad respiratoria crónica'
        ]);
        $pathology->id = 1;

        $repository
            ->shouldReceive('getPathologyById')
            ->with(1)
            ->once()
            ->andReturn($pathology);

        // Act
        $result = $service->getPathologyById(1);

        // Assert
        expect($result)->toBeInstanceOf(Pathology::class)
            ->and($result->id)->toBe(1)
            ->and($result->name)->toBe('Asma')
            ->and($result->description)->toBe('Enfermedad respiratoria crónica');

        Mockery::close();
    });

    test('createPathology crea una nueva patología correctamente', function () {
        // Arrange
        $repository = Mockery::mock(PathologyRepositoryInterface::class);
        $service = new PathologyService($repository);

        $data = [
            'name' => 'Artritis',
            'description' => 'Inflamación de las articulaciones'
        ];

        $pathology = new Pathology($data);
        $pathology->id = 3;

        $repository
            ->shouldReceive('createPathology')
            ->with($data)
            ->once()
            ->andReturn($pathology);

        // Act
        $result = $service->createPathology($data);

        // Assert
        expect($result)->toBeInstanceOf(Pathology::class)
            ->and($result->id)->toBe(3)
            ->and($result->name)->toBe('Artritis')
            ->and($result->description)->toBe('Inflamación de las articulaciones');

        Mockery::close();
    });

    test('updatePathology actualiza una patología existente', function () {
        // Arrange
        $repository = Mockery::mock(PathologyRepositoryInterface::class);
        $service = new PathologyService($repository);

        $id = 2;
        $data = [
            'name' => 'Diabetes Tipo 2',
            'description' => 'Descripción actualizada'
        ];

        $repository
            ->shouldReceive('updatePathology')
            ->with($id, $data)
            ->once()
            ->andReturn(true);

        // Act
        $result = $service->updatePathology($id, $data);

        // Assert
        expect($result)->toBeTrue();

        Mockery::close();
    });

    test('updatePathology devuelve false cuando falla la actualización', function () {
        // Arrange
        $repository = Mockery::mock(PathologyRepositoryInterface::class);
        $service = new PathologyService($repository);

        $id = 999;
        $data = ['name' => 'Test'];

        $repository
            ->shouldReceive('updatePathology')
            ->with($id, $data)
            ->once()
            ->andReturn(false);

        // Act
        $result = $service->updatePathology($id, $data);

        // Assert
        expect($result)->toBeFalse();

        Mockery::close();
    });

    test('deletePathology elimina una patología correctamente', function () {
        // Arrange
        $repository = Mockery::mock(PathologyRepositoryInterface::class);
        $service = new PathologyService($repository);

        $id = 2;

        $repository
            ->shouldReceive('deletePathology')
            ->with($id)
            ->once()
            ->andReturn(true);

        // Act
        $result = $service->deletePathology($id);

        // Assert
        expect($result)->toBeTrue();

        Mockery::close();
    });

    test('deletePathology devuelve false cuando falla la eliminación', function () {
        // Arrange
        $repository = Mockery::mock(PathologyRepositoryInterface::class);
        $service = new PathologyService($repository);

        $id = 999;

        $repository
            ->shouldReceive('deletePathology')
            ->with($id)
            ->once()
            ->andReturn(false);

        // Act
        $result = $service->deletePathology($id);

        // Assert
        expect($result)->toBeFalse();

        Mockery::close();
    });

    test('el servicio delega correctamente todas las operaciones al repositorio', function () {
        // Arrange
        $repository = Mockery::mock(PathologyRepositoryInterface::class);
        $service = new PathologyService($repository);

        $collection = new Collection();
        $pathology = new Pathology(['name' => 'Test Pathology']);
        $pathology->id = 2; // Usar ID diferente a 1 para evitar patología protegida
        $data = ['name' => 'Test'];

        $repository->shouldReceive('getAllPathologies')->once()->andReturn($collection);
        $repository->shouldReceive('getPathologyById')->with(2)->once()->andReturn($pathology);
        $repository->shouldReceive('createPathology')->with($data)->once()->andReturn($pathology);
        $repository->shouldReceive('updatePathology')->with(2, $data)->once()->andReturn(true);
        $repository->shouldReceive('deletePathology')->with(2)->once()->andReturn(true);

        // Act & Assert
        expect($service->getAllPathologies())->toBe($collection);
        expect($service->getPathologyById(2))->toBe($pathology);
        expect($service->createPathology($data))->toBe($pathology);
        expect($service->updatePathology(2, $data))->toBeTrue();
        expect($service->deletePathology(2))->toBeTrue();

        Mockery::close();
    });

    test('updatePathology lanza excepción cuando se intenta editar patología protegida (ID = 1)', function () {
        // Arrange
        $repository = Mockery::mock(PathologyRepositoryInterface::class);
        $service = new PathologyService($repository);

        $id = 1; // Patología protegida
        $data = ['name' => 'Test'];

        // El repositorio no debería ser llamado
        $repository->shouldNotReceive('updatePathology');

        // Act & Assert
        expect(fn() => $service->updatePathology($id, $data))
            ->toThrow(\Exception::class, 'Esta patología no se puede editar');

        Mockery::close();
    });

    test('deletePathology lanza excepción cuando se intenta eliminar patología protegida (ID = 1)', function () {
        // Arrange
        $repository = Mockery::mock(PathologyRepositoryInterface::class);
        $service = new PathologyService($repository);

        $id = 1;

        // El repositorio no debería ser llamado
        $repository->shouldNotReceive('deletePathology');

        // Act & Assert
        expect(fn() => $service->deletePathology($id))
            ->toThrow(\Exception::class, 'Esta patología no se puede eliminar');

        Mockery::close();
    });

    test('updatePathology permite editar patologías no protegidas (ID != 1)', function () {
        // Arrange
        $repository = Mockery::mock(PathologyRepositoryInterface::class);
        $service = new PathologyService($repository);

        $id = 2;
        $data = ['name' => 'Test'];

        $repository
            ->shouldReceive('updatePathology')
            ->with($id, $data)
            ->once()
            ->andReturn(true);

        // Act
        $result = $service->updatePathology($id, $data);

        // Assert
        expect($result)->toBeTrue();

        Mockery::close();
    });

    test('deletePathology permite eliminar patologías no protegidas (ID != 1)', function () {
        // Arrange
        $repository = Mockery::mock(PathologyRepositoryInterface::class);
        $service = new PathologyService($repository);

        $id = 2;

        $repository
            ->shouldReceive('deletePathology')
            ->with($id)
            ->once()
            ->andReturn(true);

        // Act
        $result = $service->deletePathology($id);

        // Assert
        expect($result)->toBeTrue();

        Mockery::close();
    });
});
