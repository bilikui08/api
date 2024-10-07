<?php

namespace Src\Infrastructure\Controller;

use Src\Infrastructure\Http\Request;
use Src\Domain\Repository\RepositoryInterface;
use Firebase\JWT\JWT;
use OpenApi\Attributes as OA;

class AuthController extends AbstractController
{
    #[OA\Post(
        path: "/api/auth",
        summary: "Autenticación a la aplicación por username y password",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "username", type: "string", description: "Nombre del usuario"),
                    new OA\Property(property: "password", type: "string", description: "Contraseña del usuario")
                ],
                example: [
                    "username" => "admin",
                    "password" => "verifarmaApi"
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Devuelve el Token",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Auth")
                )
            )
        ]
    )]
    #[OA\Schema(
        schema: "Auth",
        type: "object",
        description: "Modelo de Auth",
        properties: [
            new OA\Property(property: "token", type: "integer", description: "Bearer Token"),
        ]
    )]
    public function __invoke(Request $request, RepositoryInterface $userRepository): string
    {
        $privateKey = file_get_contents(getenv('PRIVATE_KEY_PATH'));

        $username = $request->username ?? null;
        $password = $request->password ?? null;

        $user = $userRepository->auth($username, $password);

        if ($user) {
            $issuedAt = time();
            $expirationTime = $issuedAt + getenv('EXPIRATION_TIME');
            $payload = [
                'iss' => getenv('DEFAULT_URI'),
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'userId' => $user->getId(),
            ];

            $jwt = JWT::encode($payload, $privateKey, 'RS256');
            $user->setToken($jwt);
            $userRepository->insertOrUpdate($user);

            return $this->json(['token' => $jwt]);
        }

        return $this->error('Bad credentials', 404);
    }
}
