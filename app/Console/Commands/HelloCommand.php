<?php

namespace App\Console\Commands;

use App\Console\CommandInterface;

class HelloCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'hello';
    }

    public function getDescription(): string
    {
        return 'Muestra un mensaje de saludo.';
    }

    public function handle(array $args): void
    {
        $name = $args[0] ?? 'Mundo';
        echo "¡Hola, {$name}!" . PHP_EOL;
    }
}
