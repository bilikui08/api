<?php

namespace Src\Infrastructure\Http;

class Request
{
    public static ?Request $request = null;
    protected array $headers = [];
    protected array $all = [];
    protected array $get = [];
    protected array $post = [];
    protected ?array $body = [];

    public static function createRequest(): self
    {
        if (null === self::$request) {
            self::$request = new self();
        }

        return self::$request;
    }

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->headers = $this->getRequestHeaders();
        $this->body = $this->getBody();
        $this->all['get'] = $this->get;
        $this->all['post'] = $this->post;
        $this->all['headers'] = $this->headers;
        $this->all['body'] = $this->body;
    }

    public function __get(string $key): string|null
    {
        $return = null;
        if (!empty($this->get) && isset($this->get[$key])) {
            return $this->get[$key];
        }

        if (!empty($this->post) && isset($this->post[$key])) {
            return $this->post[$key];
        }

        if (!empty($this->body) && isset($this->body[$key])) {
            return $this->body[$key];
        }

        return $return;
    }

    public function setUriParam(array $param): self
    {
        $this->get = $param;
        return $this;
    }

    public function headers(): array|null
    {
        return $this->headers;
    }

    public function all(): array
    {
        return $this->all;
    }

    public function isMethod(string $method): bool
    {
        return $_SERVER['REQUEST_METHOD'] == strtoupper(trim($method));
    }

    public function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'];
        $parsedUri = parse_url($uri);
        return $parsedUri['path'];
    }

    public function getQueryString(): string
    {
        return '?' . $_SERVER['QUERY_STRING'];
    }

    private function getRequestHeaders(): array|null
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }

        return $headers;
    }

    public function getBody(): array|null
    {
        $requestBody = file_get_contents("php://input");
        return json_decode($requestBody, true);
    }
}
