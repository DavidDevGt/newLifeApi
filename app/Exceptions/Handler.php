<?php

namespace App\Exceptions;

class Handler
{
    /**
     * Mapeo de códigos de error a claves.
     */
    protected $errorType = [
        404 => 'not_found',
        403 => 'forbidden',
        401 => 'unauthorized',
        400 => 'bad_request',
        500 => 'internal_server_error',
        503 => 'service_unavailable',
        504 => 'gateway_timeout',
        429 => 'too_many_requests'
    ];

    /**
     * Mapeo de claves de error a mensajes.
     */
    protected $errorMessages = [
        'not_found' => 'Not Found',
        'forbidden' => 'Forbidden',
        'unauthorized' => 'Unauthorized',
        'bad_request' => 'Bad Request',
        'internal_server_error' => 'Internal Server Error',
        'service_unavailable' => 'Service Unavailable',
        'gateway_timeout' => 'Gateway Timeout',
        'too_many_requests' => 'Too Many Requests'
    ];

    /**
     * Retorna una respuesta JSON con el error.
     *
     * @param int $code Código HTTP de error.
     * @param string|null $customMessage Mensaje personalizado (opcional).
     * @return string JSON con la información del error.
     */
    public function showError(int $code, string $customMessage = null): string
    {
        if (!isset($this->errorType[$code])) {
            $code = 500;
        }

        $errorKey = $this->errorType[$code];
        $message = $customMessage ?? $this->errorMessages[$errorKey];

        http_response_code($code);

        return json_encode([
            'error' => [
                'code' => $code,
                'type' => $errorKey,
                'message' => $message,
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Retorna una página HTML con el error (Eso se manejara en el front pero si se necesita mostrar html en el back se puede usar).
     *
     * @param int $code Código HTTP de error.
     * @param string|null $customMessage Mensaje personalizado (opcional).
     * @return string HTML con la información del error.
     */
    public function showThisPage(int $code, string $customMessage = null): string
    {
        if (!isset($this->errorType[$code])) {
            $code = 500;
        }

        $errorKey = $this->errorType[$code];
        $message = $customMessage ?? $this->errorMessages[$errorKey];

        http_response_code($code);

        $html = <<<HTML
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <title>Error $code - $message</title>
                        <style>
                            body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; background-color: #f2f2f2; }
                            h1 { font-size: 48px; color: #cc0000; font-weight: bold; margin-bottom: 20px; }
                            p { font-size: 24px; color: #2C2C2C; padding: 20px 0; }
                        </style>
                    </head>
                    <body>
                        <h1>Error $code</h1>
                        <p>$message</p>
                    </body>
                    </html>
                    HTML;
        return $html;
    }
}
