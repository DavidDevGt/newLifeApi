<?php

namespace App\Handlers;

class JsonHandler
{
    /**
     * Envía una respuesta JSON y establece el código de estado HTTP.
     *
     * @param mixed $data    Datos a enviar en la respuesta.
     * @param int   $status  Código de estado HTTP (por defecto 200).
     */
    public static function send($data, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
