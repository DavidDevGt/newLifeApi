<?php

namespace App\Core;

use Medoo\Medoo;
use PDO;
use PDOException;
use Exception;

/**
 * Manages database connections using the Medoo ORM.
 *
 * Implements a Singleton pattern to ensure only one
 * database connection instance exists throughout the application.
 *
 * @package App\Core
 */
class Database
{
    /**
     * Holds the Medoo instance.
     *
     * @var Medoo|null
     */
    private static ?Medoo $instance = null;

    /**
     * Environment variable names.
     */
    private const DB_CONNECTION = 'DB_CONNECTION';
    private const DB_HOST       = 'DB_HOST';
    private const DB_PORT       = 'DB_PORT';
    private const DB_DATABASE   = 'DB_DATABASE';
    private const DB_USERNAME   = 'DB_USERNAME';
    private const DB_PASSWORD   = 'DB_PASSWORD';

    /**
     * Returns the single database connection instance.
     *
     * @return Medoo
     * @throws Exception If the connection cannot be established.
     */
    public static function getInstance(): Medoo
    {
        if (self::$instance === null) {
            self::$instance = self::connect();
        }

        return self::$instance;
    }

    /**
     * Creates the database connection using Medoo.
     *
     * @return Medoo
     * @throws Exception If a required environment variable is missing or the connection fails.
     */
    private static function connect(): Medoo
    {
        try {
            $requiredEnvVars = [
                self::DB_CONNECTION,
                self::DB_HOST,
                self::DB_PORT,
                self::DB_DATABASE,
                self::DB_USERNAME,
                self::DB_PASSWORD
            ];
            
            // Validate that all required environment variables are set
            foreach ($requiredEnvVars as $var) {
                if (!array_key_exists($var, $_ENV)) {
                    throw new Exception("The environment variable {$var} is missing.");
                }
            }            

            $config = [
                'type'     => $_ENV[self::DB_CONNECTION] ?? 'mysql',
                'host'     => $_ENV[self::DB_HOST]       ?? '127.0.0.1',
                'port'     => $_ENV[self::DB_PORT]       ?? '3306',
                'database' => $_ENV[self::DB_DATABASE]   ?? '',
                'username' => $_ENV[self::DB_USERNAME]   ?? '',
                'password' => $_ENV[self::DB_PASSWORD]   ?? '',
                'charset'  => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'error'    => PDO::ERRMODE_EXCEPTION,
                'command'  => [
                    'SET SQL_MODE=ANSI_QUOTES'
                ],
                'option'   => [
                    PDO::ATTR_EMULATE_PREPARES    => false,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ],
            ];

            return new Medoo($config);
        } catch (PDOException $e) {
            error_log("Error connecting to the database: " . $e->getMessage());
            throw new Exception(
                "Could not establish a connection to the database. " .
                "Please check your configuration."
            );
        }
    }

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Prevents cloning of this instance.
     */
    private function __clone()
    {
    }
}
