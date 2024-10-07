<?php

namespace Src\Infrastructure\Controller\Farmacia;

use Src\Domain\Repository\RepositoryInterface;
use Src\Infrastructure\Controller\AbstractController;
use Src\Infrastructure\Http\Request;
use OpenApi\Attributes as OA;

class GetByIdController extends AbstractController
{
    #[OA\Get(
        path: "/api/farmacia/{id}",
        summary: "Obtiene una farmacia por ID",
        tags: ["Farmacias"],
        security: ["Authorization"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID de la farmacia",
                required: true,
                schema: new OA\Schema(type: "integer")
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
    public function __invoke(Request $request, RepositoryInterface $farmaciaRepository): string
    {
        $farmacia = $farmaciaRepository->findById($request->id);
        if (!$farmacia) {
            return $this->error('No results found', 404);
        }

        return $this->json($farmacia->toReturnArray());
    }
}
