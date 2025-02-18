<?php
// declare types for strict mode in PHP
declare(strict_types=1);

use App\Core\Router;
use App\Handlers\JsonHandler;
use App\Exceptions\Handler;
use App\Middleware\ApiMiddleware;

class ResponseHandler {
    /**
     * Envía una respuesta formateada en JSON.
     *
     * @param mixed $data Los datos a enviar en la respuesta.
     * @param int $status El código HTTP de la respuesta.
     * @param bool $error Indica si la respuesta representa un error.
     * @return void
     */
    public function send($data, int $status = 200, bool $error = false): void 
    {
        date_default_timezone_set('America/Guatemala');

        if ($error) {
            if ($data instanceof \Throwable) {
                $data = ['error' => $data->getMessage()];
            } elseif (is_string($data)) {
                $data = ['error' => $data];
            } elseif (is_array($data) && !isset($data['error'])) {
                $data = ['error' => $data];
            }
        }

        $response = [
            'status'    => $status,
            'timestamp' => date('c'),
            'data'      => $data
        ];

        if ($error) {
            $response['error'] = true;
        }

        JsonHandler::send($response, $status);
    }

    /**
     * Envía una respuesta de error.
     *
     * @param mixed $data El mensaje o la excepción de error.
     * @param int $status El código HTTP de error.
     * @return void
     */
    public function error($data, int $status = 400): void 
    {
        if ($data instanceof \Throwable) {
            $data = $data->getMessage();
        }
        $this->send($data, $status, true);
    }

    /**
     * Envía una respuesta de éxito.
     *
     * @param mixed $data Los datos a enviar.
     * @param string $message Un mensaje adicional (opcional).
     * @param int $status El código HTTP de la respuesta.
     * @return void
     */
    public function success($data, string $message = '', int $status = 200): void 
    {
        $responseData = is_array($data) ? $data : ['data' => $data];
        if ($message) {
            $responseData['message'] = $message;
        }
        $this->send($responseData, $status);
    }
}

class RequestHandler {
    /**
     * Obtiene y decodifica los datos JSON del cuerpo de la petición.
     *
     * @return array Los datos decodificados o un array vacío en caso de error.
     */
    public function getJsonData(): array 
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Opcional: puedes registrar el error de json_decode() aquí
            return [];
        }
        return $data ?? [];
    }
}

$response = new ResponseHandler();
$request = new RequestHandler();
$router = new Router();

// Metadata de la API
const API_METADATA = [
    'name' => 'API David',
    'version' => '1.0.0',
    'endpoints' => [
        'tasks'    => '/tasks',
        'expenses' => '/expenses',
        'incomes'  => '/incomes'
    ]
];

$router->get('/', function() use ($response) {
    $response->success(API_METADATA);
});

// Ruta OPTIONS global para manejo de CORS
$router->options('/(.*)', function() {
    http_response_code(200);
    exit();
});

// Rutas para Tasks
$router->get('/tasks', function() use ($response) {
    $taskModel = new \App\Models\Task();
    $response->success($taskModel->all());
});

$router->post('/tasks', function() use ($response, $request) {
    $taskModel = new \App\Models\Task();
    $data = $request->getJsonData();

    if ($taskModel->create($data)) {
        $response->success(null, "Tarea creada", 201);
    } else {
        $response->error("No se pudo crear la tarea");
    }
});

$router->get('/tasks/{id}', function($id) use ($response) {
    $taskModel = new \App\Models\Task();
    $task = $taskModel->find((int)$id);

    if ($task) {
        $response->success($task);
    } else {
        $response->error("Tarea no encontrada", 404);
    }
});

$router->put('/tasks/{id}', function($id) use ($response, $request) {
    $taskModel = new \App\Models\Task();
    $data = $request->getJsonData();

    if ($taskModel->update((int)$id, $data)) {
        $response->success(null, "Tarea actualizada");
    } else {
        $response->error("No se pudo actualizar la tarea");
    }
});

$router->delete('/tasks/{id}', function($id) use ($response) {
    $taskModel = new \App\Models\Task();

    if ($taskModel->delete((int)$id)) {
        $response->success(null, "Tarea eliminada");
    } else {
        $response->error("No se pudo eliminar la tarea");
    }
});

// Rutas para Expenses
$router->get('/expenses', function() use ($response) {
    $expenseModel = new \App\Models\Expense();
    $response->success($expenseModel->all());
});

$router->post('/expenses', function() use ($response, $request) {
    $expenseModel = new \App\Models\Expense();
    $data = $request->getJsonData();

    if ($expenseModel->create($data)) {
        $response->success(null, "Gasto registrado", 201);
    } else {
        $response->error("No se pudo registrar el gasto");
    }
});

$router->get('/expenses/{id}', function($id) use ($response) {
    $expenseModel = new \App\Models\Expense();
    $expense = $expenseModel->find((int)$id);

    if ($expense) {
        $response->success($expense);
    } else {
        $response->error("Gasto no encontrado", 404);
    }
});

$router->put('/expenses/{id}', function($id) use ($response, $request) {
    $expenseModel = new \App\Models\Expense();
    $data = $request->getJsonData();

    if ($expenseModel->update((int)$id, $data)) {
        $response->success(null, "Gasto actualizado");
    } else {
        $response->error("No se pudo actualizar el gasto");
    }
});

$router->delete('/expenses/{id}', function($id) use ($response) {
    $expenseModel = new \App\Models\Expense();

    if ($expenseModel->delete((int)$id)) {
        $response->success(null, "Gasto eliminado");
    } else {
        $response->error("No se pudo eliminar el gasto");
    }
});

// Rutas para Incomes
$router->get('/incomes', function() use ($response) {
    $incomeModel = new \App\Models\Income();
    $response->success($incomeModel->all());
});

$router->post('/incomes', function() use ($response, $request) {
    $incomeModel = new \App\Models\Income();
    $data = $request->getJsonData();

    if ($incomeModel->create($data)) {
        $response->success(null, "Ingreso registrado", 201);
    } else {
        $response->error("No se pudo registrar el ingreso");
    }
});

$router->get('/incomes/{id}', function($id) use ($response) {
    $incomeModel = new \App\Models\Income();
    $income = $incomeModel->find((int)$id);

    if ($income) {
        $response->success($income);
    } else {
        $response->error("Ingreso no encontrado", 404);
    }
});

$router->put('/incomes/{id}', function($id) use ($response, $request) {
    $incomeModel = new \App\Models\Income();
    $data = $request->getJsonData();

    if ($incomeModel->update((int)$id, $data)) {
        $response->success(null, "Ingreso actualizado");
    } else {
        $response->error("No se pudo actualizar el ingreso");
    }
});

$router->delete('/incomes/{id}', function($id) use ($response) {
    $incomeModel = new \App\Models\Income();

    if ($incomeModel->delete((int)$id)) {
        $response->success(null, "Ingreso eliminado");
    } else {
        $response->error("No se pudo eliminar el ingreso");
    }
});

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($currentPath !== '/') {
    $apiMiddleware = new ApiMiddleware();
    if (!$apiMiddleware->handle()) {
        exit;
    }
}

try {
    $router->run();
} catch (Throwable $exception) {
    $handler = new Handler();
    $response->error($exception, 500);
}
