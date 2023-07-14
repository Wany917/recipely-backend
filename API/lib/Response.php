<?php
namespace Recipely\Lib;
class Response
{
    private int $statusCode;
    private array $headers;
    private mixed $body;

    private function sendStatusCode(): void
    {
        http_response_code($this->statusCode);
    }

    private function sendHeaders(): void
    {
        foreach ($this->headers as $headerName => $headerValue) {
            header("$headerName: $headerValue");
        }
    }

    public static function create(): self
    {
        return new self();
    }

    public function __construct(int $statusCode = 200)
    {
        $this->statusCode = $statusCode;
        $this->headers = [];
        $this->body = null;
    }

    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function withStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function withBody(mixed $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function json(): string
    {
        $this->withHeader('Content-Type', 'application/json');
        $this->sendStatusCode();
        $this->sendHeaders();

        return json_encode($this->body);
    }

    public function text(): string
    {
        $this->withHeader('Content-Type', 'text/plain');
        $this->sendStatusCode();
        $this->sendHeaders();

        return (string) $this->body;
    }

    public function sendResponse(int $code, bool $success, mixed $message, mixed $data = null): String
    {
        $body = [
            "Success:" => $success,
            "message" => $message,
        ];
        if ($data !== null) {
            $body['data'] = $data;
        }
        return $this->withStatusCode($code)
        ->withHeader('X-Server', 'RECIPELY')
        ->withBody($body)
        ->json();
    }

    public function sendAuthResponse(int $code, bool $success, mixed $message, mixed $data = null): String
    {
        $body = [
            "Success:" => $success,
            "message" => $message,
        ];

        if ($data !== null) {
            foreach ($data as $key => $value) {
                $body[$key] = $value;
            }
        }

        return $this->withStatusCode($code)
            ->withHeader('X-Server', 'RECIPELY')
            ->withBody($body)
            ->json();
    }


}
