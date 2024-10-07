<?php

namespace Src\Tests;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Src\Infrastructure\Persistence\Repository\FarmaciaRepository;
use Src\Infrastructure\Persistence\Repository\AbstractRepository;
use Src\Domain\Model\Farmacia;
use Src\Domain\Service\CoordinateDistanceService;
use Src\Tests\Request;
use Dotenv\Dotenv;

class ClosestFarmaciaTest extends TestCase
{
    public function testClosestFarmacia(): void
    {
        Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();

        $request = Request::createRequest();

        $random = rand();
        $this->insertFarmaciaCercana($random);

        $mockRequestBody = [
            'lat' => '-34.91335',
            'lon' => '-58.385837',
        ];

        $request->setUriParam($mockRequestBody);

        $farmaciaRepository = AbstractRepository::create(FarmaciaRepository::class);

        $farmacias = $farmaciaRepository->findAll();
        $branches = [];
        foreach ($farmacias as $key => $farmacia) {
            $branches[$key]['id'] = $farmacia->getId();
            $branches[$key]['name'] = $farmacia->getNombre();
            $branches[$key]['lat'] = (float) $farmacia->getLatitud();
            $branches[$key]['lon'] = (float) $farmacia->getLongitud();
        }

        $coordinateDistanceService = new CoordinateDistanceService();
        $closestBranch = $coordinateDistanceService
            ->findClosestBranch((float) $request->lat, (float) $request->lon, $branches);

        $this->assertTrue($closestBranch['name'] === 'Farmacia Test Case ' . $random);

        $id = $closestBranch['id'];

        // Borro el registro generado por el test
        $this->delete($id);
    }

    public function insertFarmaciaCercana(int $random): void
    {
        $farmaciaRepository = AbstractRepository::create(FarmaciaRepository::class);

        $request = Request::createRequest();

        $mockRequestBody = [
            'nombre' => 'Farmacia Test Case ' . $random,
            'direccion' => 'Dirección Test',
            'latitud' => '-34.91336',
            'longitud' => '-58.385838',
        ];

        $request->setBody($mockRequestBody);

        $farmacia = $this->handleRequest($request);

        $farmaciaRepository = AbstractRepository::create(FarmaciaRepository::class);

        $farmaciaRepository->insertOrUpdate($farmacia);
    }

    public function delete(int $id): void
    {
        $farmaciaRepository = AbstractRepository::create(FarmaciaRepository::class);

        $farmaciaRepository->delete($id);
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
}
