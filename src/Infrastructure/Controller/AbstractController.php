<?php

namespace Src\Infrastructure\Controller;

use Throwable;
use Monolog\Logger;
use Src\Domain\Exception\AuthorizationException;
use Src\Domain\Model\ModelInterface;
use Src\Domain\Model\User;
use Src\Infrastructure\Http\Request;
use Src\Infrastructure\Persistence\Repository\AbstractRepository;
use Src\Infrastructure\Persistence\Repository\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class AbstractController
{
    private ?User $user = null;
    private ?Logger $logger = null;

    public function __construct(bool $authorization, Logger $logger)
    {
        $this->logger = $logger;

        if ($authorization) {
            $this->user = $this->getUserToken();
        }
    }

    protected function getLogger(): Logger|null
    {
        return $this->logger;
    }

    protected function getUser(): User|null
    {
        return $this->user;
    }

    protected function json(string|array $response, int $httpResponse = 200): string
    {
        if (is_string($response)) {
            $jsonResponse = ['message' => $response];
        } else {
            $jsonResponse = $response;
        }
        http_response_code($httpResponse);
        header('Content-type: application/json');
        return json_encode($jsonResponse);
    }

    protected function error(string|array $errorMessage, int $httpResponse = 500): string
    {
        if (is_string($errorMessage)) {
            $jsonResponse = ['message' => $errorMessage];
        } else {
            $jsonResponse = $errorMessage;
        }

        http_response_code($httpResponse);
        header('Content-type: application/json');
        return json_encode($jsonResponse);
    }

    protected function getRequest(): Request
    {
        return Request::createRequest();
    }

    private function getUserToken(): User|string
    {
        $token = $this->getBearerToken();
        if (null === $token) {
            throw new AuthorizationException('No bearer token present.');
        }

        try {
            $publicKey = file_get_contents(getenv('PUBLIC_KEY_PATH'));
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
            return AbstractRepository::create(UserRepository::class)->findById($decoded->userId);
        } catch (ExpiredException $expiredException) {
            throw new AuthorizationException('Token expired');
        } catch (SignatureInvalidException $signatureInvalidException) {
            throw new AuthorizationException('Signature invalid');
        } catch (Throwable $throwable) {
            throw new AuthorizationException('Invalid token: ' . $throwable->getMessage());
        }
    }
    private function getBearerToken(): string|null
    {
        $request = $this->getRequest();
        $headers = $request->headers();
        if (null !== $headers) {
            if (isset($headers['Authorization'])) {
                if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                    return $matches[1];
                }
            }
        }

        return null;
    }
}
