<?php

declare(strict_types=1);

namespace App\Middleware;

use InvalidArgumentException;

enum ApiKeyErrorType: string
{
    case MISSING = 'MISSING API KEY';
    case INVALID = 'INVALID API KEY';
}

class ApiResponse
{
    public function __construct(
        public string $error,
        public string $message
    ) {}
}

class ApiMiddleware
{
    public const HEADER_NAME = 'X-Api-Key';

    public function __construct(
        private array $validApiKeys = []
    ) {
        $this->validApiKeys = [$_ENV['APP_KEY']];
        $this->validateApiKeys($this->validApiKeys);
    }

    public function handle(): bool
    {
        $headers = getallheaders();
        $apiKey = $headers[self::HEADER_NAME] ?? null;

        if (!$apiKey) {
            $this->sendErrorResponse(
                ApiKeyErrorType::MISSING,
                'API KEY not provided',
                401
            );
            return false;
        }

        if (!in_array($apiKey, $this->validApiKeys, true)) {
            $this->sendErrorResponse(
                ApiKeyErrorType::INVALID,
                'Invalid API KEY',
                403
            );
            return false;
        }

        return true;
    }

    public function setValidApiKeys(array $keys): void
    {
        $this->validateApiKeys($keys);
        $this->validApiKeys = $keys;
    }

    private function validateApiKeys(array $keys): void
    {
        if (empty($keys)) {
            throw new InvalidArgumentException('El array de API keys no puede estar vacío');
        }

        foreach ($keys as $key) {
            if (!is_string($key) || empty(trim($key))) {
                throw new InvalidArgumentException('Todas las API keys deben ser strings no vacíos');
            }
        }
    }

    private function sendErrorResponse(ApiKeyErrorType $errorType, string $message, int $statusCode): void
    {
        http_response_code($statusCode);

        $response = new ApiResponse(
            error: $errorType->value,
            message: $message
        );

        echo json_encode($response, JSON_THROW_ON_ERROR);
    }
}
