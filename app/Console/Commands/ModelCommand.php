<?php

namespace App\Console\Commands;

use App\Console\CommandInterface;
use RuntimeException;

class ModelCommand implements CommandInterface
{
    private const MODEL_TEMPLATE = <<<'PHP'
<?php

namespace App\Models;

use App\Models\BaseModel;

class %s extends BaseModel
{
    protected string $table = '%s';
}
PHP;

    /**
     * Nombre del comando
     */
    public function getName(): string
    {
        return 'make:model';
    }

    /**
     * Descripción del comando
     */
    public function getDescription(): string
    {
        return 'Crea un nuevo modelo en la carpeta app/Models';
    }

    /**
     * Ejecuta la lógica del comando
     */
    public function handle(array $args): void
    {
        $this->validateInput($args);

        $modelName = $args[0];
        $tableName = $this->getTableName($modelName);

        $this->createModelDirectory();
        $this->createModelFile($modelName, $tableName);
    }

    /**
     * Valida los argumentos de entrada
     */
    private function validateInput(array $args): void
    {
        if (empty($args)) {
            throw new RuntimeException(
                "Uso: php console.php make:model NombreDelModelo\n" .
                    "Ejemplo: php console.php make:model User"
            );
        }

        $modelName = $args[0];
        if (!preg_match('/^[A-Z][A-Za-z0-9]*$/', $modelName)) {
            throw new RuntimeException(
                "Error: El nombre del modelo debe:\n" .
                    "- Comenzar con letra mayúscula\n" .
                    "- Contener solo letras y números\n" .
                    "- Seguir la convención PascalCase"
            );
        }
    }

    /**
     * Obtiene el nombre de la tabla a partir del nombre del modelo
     */
    private function getTableName(string $modelName): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $modelName)) . 's';
    }

    /**
     * Crea el directorio de modelos si no existe
     */
    private function createModelDirectory(): void
    {
        $modelDirectory = $this->getModelDirectory();

        if (!is_dir($modelDirectory) && !mkdir($modelDirectory, 0755, true)) {
            throw new RuntimeException("Error: No se pudo crear el directorio de modelos");
        }
    }

    /**
     * Crea el archivo del modelo
     */
    private function createModelFile(string $modelName, string $tableName): void
    {
        $filePath = $this->getModelDirectory() . "/{$modelName}.php";

        if (file_exists($filePath)) {
            throw new RuntimeException("Error: El modelo {$modelName} ya existe");
        }

        $content = sprintf(self::MODEL_TEMPLATE, $modelName, $tableName);

        if (file_put_contents($filePath, $content) === false) {
            throw new RuntimeException("Error: No se pudo crear el archivo del modelo");
        }

        echo "✓ Modelo {$modelName} creado exitosamente en app/Models/{$modelName}.php" . PHP_EOL;
    }

    /**
     * Obtiene la ruta al directorio de modelos
     */
    private function getModelDirectory(): string
    {
        return __DIR__ . '/../../Models';
    }
}
