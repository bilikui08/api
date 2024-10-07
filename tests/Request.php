<?php

namespace Src\Tests;

use PHPUnit\Framework\TestCase;
use Src\Infrastructure\Http\Request as BaseRequest;

final class Request extends BaseRequest
{
    public static function createRequest(): self
    {
        if (null === self::$request) {
            self::$request = new self();
        }

        return self::$request;
    }

    public function __construct()
    {
        $this->body = $this->getBody();
    }

    public function setBody(array $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getBody(): array|null
    {
        return $this->body;
    }
}
