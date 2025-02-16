<?php

namespace App\Console;

use App\Console\Commands\HelloCommand;
use App\Console\Commands\ModelCommand;

/**
 * The Console Kernel - manages command registration and execution.
 * 
 * Acts as the central entry point for handling console commands within the application.
 * Responsible for registering available commands and dispatching them based on user input.
 */
class Kernel
{
    /**
     * Registered command instances.
     * 
     * @var array<string, CommandInterface> 
     * Array where keys are command names and values are command instances
     */
    protected array $commands = [];

    /**
     * Initialize the console kernel.
     * 
     * Registers default application commands during instantiation.
     */
    public function __construct()
    {
        // ─────────────────────────────────────────────
        //    Registrar comandos en el constructor    //
        // ─────────────────────────────────────────────
        $this->register(new HelloCommand());
        $this->register(new ModelCommand());
    }

    /**
     * Register a new command with the kernel.
     * 
     * @param CommandInterface $command The command instance to register
     * @return void
     */
    public function register(CommandInterface $command): void
    {
        $this->commands[$command->getName()] = $command;
    }

    /**
     * Execute the console command based on input arguments.
     * 
     * Processes command line arguments and delegates execution to the appropriate command handler.
     * Displays help information when no valid command is provided.
     * 
     * @param array $argv Command line arguments (typically from $_SERVER['argv'])
     * @return void
     */
    public function run(array $argv): void
    {
        if (count($argv) < 2) {
            $this->printHelp();
            return;
        }

        $commandName = $argv[1];
        $args = array_slice($argv, 2);

        if (isset($this->commands[$commandName])) {
            $this->commands[$commandName]->handle($args);
        } else {
            echo "Comando no encontrado: {$commandName}" . PHP_EOL;
            $this->printHelp();
        }
    }

    /**
     * Display help information with available commands.
     * 
     * Prints basic usage instructions and a list of registered commands with their descriptions.
     * 
     * @return void
     */
    protected function printHelp(): void
    {
        echo "Uso: php console.php <comando> [argumentos]" . PHP_EOL;
        echo "Comandos disponibles:" . PHP_EOL;
        foreach ($this->commands as $command) {
            echo "  - " . $command->getName() . ": " . $command->getDescription() . PHP_EOL;
        }
    }
}