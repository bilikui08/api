<?php

declare(strict_types=1);

namespace Src\Tests;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Src\Infrastructure\Persistence\Repository\FarmaciaRepository;
use Src\Infrastructure\Persistence\Repository\AbstractRepository;
use Src\Domain\Model\Farmacia;
use Src\Tests\Request;
use Src\Application\Validator\Request\FarmaciaValidator;
use Dotenv\Dotenv;

final class FarmaciaTest extends TestCase
{
    public function testSucessInsertFarmacia(): void
    {
        Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();

        $request = Request::createRequest();

        $mockRequestBody = [
            'nombre' => 'Farmacia Test',
            'direccion' => 'Dirección Test',
            'latitud' => '-34.810896',
            'longitud' => '-58.3702655',
        ];

        $request->setBody($mockRequestBody);

        $validator = new FarmaciaValidator();

        if (!$validator->isValid($request)) {
            $errors = $validator->getErrors();
        }

        $farmacia = $this->handleRequest($request);

        $farmaciaRepository = AbstractRepository::create(FarmaciaRepository::class);

        $result = $farmaciaRepository->insertOrUpdate($farmacia);

        $this->assertTrue($result);

        // Borro el registro generado por el test
        $this->delete($farmacia->getId());
    }

    public function testNotBlankNombreErrorInsertFarmacia(): void
    {
        Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();

        $request = Request::createRequest();

        $mockRequestBody = [
            'nombre' => '',
            'direccion' => 'Dirección Test',
            'latitud' => '-34.810896',
            'longitud' => '-58.3702655',
        ];

        $request->setBody($mockRequestBody);

        $validator = new FarmaciaValidator();

        if (!$validator->isValid($request)) {
            $errors = $validator->getErrors();
            $isError = ($errors[0]['field'] === 'nombre' && $errors[0]['message'] === 'El nombre es obligatorio.');
            $this->assertTrue($isError);
        }
    }

    public function testMaxLengthNombreErrorInsertFarmacia(): void
    {
        Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();

        $request = Request::createRequest();

        $mockRequestBody = [
            'nombre' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero. Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Nullam accumsan lorem in dui. Cras ultricies mi eu turpis hendrerit fringilla. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; In ac dui quis mi consectetuer lacinia. Nam pretium turpis et arcu. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Sed aliquam ultrices mauris. Integer ante arcu, accumsan a, consectetuer eget, posuere ut, mauris. Praesent adipiscing. Phasellus ullamcorper ipsum rutrum nunc. Nunc nonummy metus. Vestibulum volutpat pretium libero. Cras id dui. Aenean ut',
            'direccion' => 'Dirección Test',
            'latitud' => '-34.810896',
            'longitud' => '-58.3702655',
        ];

        $request->setBody($mockRequestBody);

        $validator = new FarmaciaValidator();

        if (!$validator->isValid($request)) {
            $errors = $validator->getErrors();
            $isError = (
                $errors[0]['field'] === 'nombre'
                && $errors[0]['message'] === 'El nombre no puede superar los 255 caracteres.'
            );
            $this->assertTrue($isError);
        }
    }

    protected function handleRequest(Request $request): Farmacia
    {
        $data = $request->getBody();

        $id = $data['id'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $direccion = $data['direccion'] ?? null;
        $latitud = $data['latitud'] ?? null;
        $longitud = $data['longitud'] ?? null;

        return new Farmacia($id, $nombre, $direccion, $latitud, $longitud, new DateTimeImmutable());
    }

    public function delete(int $id): void
    {
        $farmaciaRepository = AbstractRepository::create(FarmaciaRepository::class);

        $farmaciaRepository->delete($id);
    }
}
