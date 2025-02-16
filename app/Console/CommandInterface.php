<?php

namespace App\Console;

/**
 * Interface for console commands.
 */
interface CommandInterface
{
    /**
     * Returns the name of the command.
     *
     * @return string The command name.
     */
    public function getName(): string;

    /**
     * Returns a brief description of the command.
     *
     * @return string The command description.
     */
    public function getDescription(): string;

    /**
     * Executes the command logic.
     *
     * @param array $args Arguments passed from the command line.
     * @return void
     */
    public function handle(array $args): void;
}
