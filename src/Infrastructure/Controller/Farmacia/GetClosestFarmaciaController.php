<?php

namespace Src\Infrastructure\Controller\Farmacia;

use Src\Domain\Repository\RepositoryInterface;
use Src\Domain\Service\CoordinateDistanceService;
use Src\Infrastructure\Controller\AbstractController;
use Src\Infrastructure\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: "/api/farmacia",
    summary: "Obtiene la farmacia más cercana dados una latitud y longitud pasado por parámetro",
    tags: ["Farmacias"],
    security: ["bearerAuth"],
    parameters: [
        new OA\Parameter(
            name: "lat",
            in: "query",
            description: "Latitud",
            required: true,
            schema: new OA\Schema(type: "float")
        ),
        new OA\Parameter(
            name: "lon",
            in: "query",
            description: "Longitud",
            required: true,
            schema: new OA\Schema(type: "float")
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Lista de farmacias",
            content: new OA\JsonContent(
                type: "array",
                items: new OA\Items(ref: "#/components/schemas/Farmacia")
            )
        )
    ]
)]

class GetClosestFarmaciaController extends AbstractController
{
    public function __invoke(
        Request $request,
        RepositoryInterface $farmaciaRepository,
        CoordinateDistanceService $coordinateDistanceService
    ): string {
        $farmacias = $farmaciaRepository->findAll();
        $branches = [];
        foreach ($farmacias as $key => $farmacia) {
            $branches[$key]['id'] = $farmacia->getId();
            $branches[$key]['name'] = $farmacia->getNombre();
            $branches[$key]['lat'] = (float) $farmacia->getLatitud();
            $branches[$key]['lon'] = (float) $farmacia->getLongitud();
        }

        $closestBranch = $coordinateDistanceService
            ->findClosestBranch((float) $request->lat, (float) $request->lon, $branches);

        if (empty($closestBranch)) {
            return $this->json('No se ha podido encontrar una farmacia cercana.');
        }

        $message = 'La farmacia mas cercana es ' . $closestBranch['name'];
        $message .= ' con latitud: ' . $closestBranch['lat'] . ' y';
        $message .= ' longitud: ' . $closestBranch['lon'];

        $jsonResponse['results']['message'] = $message;
        $jsonResponse['results']['farmaciaId'] = $closestBranch['id'];

        return $this->json($jsonResponse);
    }
}
