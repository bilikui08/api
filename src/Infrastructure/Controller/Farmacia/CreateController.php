<?php

namespace Src\Infrastructure\Controller\Farmacia;

use DateTimeImmutable;
use PDOException;
use Src\Domain\Model\Farmacia;
use Src\Domain\Model\ModelInterface;
use Src\Domain\Repository\RepositoryInterface;
use Src\Infrastructure\Controller\AbstractController;
use Src\Infrastructure\Http\Request;
use Src\Application\Validator\Request\FarmaciaValidator;
use OpenApi\Attributes as OA;

class CreateController extends AbstractController
{
    #[OA\Post(
        path: "/api/farmacia",
        summary: "Inserta una farmacia",
        tags: ["Farmacias"],
        security: ["bearerAuth"],
        requestBody: new OA\RequestBody(
            description: "Datos de la farmacia a insertar",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/Farmacia")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Guarda una farmacia",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Farmacia")
                )
            )
        ]
    )]
    public function __invoke(Request $request, RepositoryInterface $farmaciaRepository): string
    {
        $validator = new FarmaciaValidator();

        if (!$validator->isValid($request)) {
            $jsonResponse['errors'] = $validator->getErrors();
            return $this->error($jsonResponse, 400);
        }

        $farmacia = $this->handleRequest($request);
        try {
            $result = $farmaciaRepository->insertOrUpdate($farmacia);
            if (!$result) {
                return $this->error('No se ha podido insertado la farmacia.', 400);
            }

            return $this->json('Se ha insertado correctamente la farmacia');

        } catch (PDOException $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    protected function handleRequest(Request $request): ModelInterface
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
