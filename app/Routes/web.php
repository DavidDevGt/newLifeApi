<?php

declare(strict_types=1);

use App\Core\Router;
use App\Handlers\JsonHandler;
use App\Exceptions\Handler;
use App\Middleware\ApiMiddleware;

/**
 * Response handler class to manage API responses
 */
class ResponseHandler {
    /**
     * Send a response
     *
     * @param mixed $data The data to send in the response
     * @param int $status The HTTP status code
     * @param bool $error Whether the response indicates an error
     */
    public function send($data, int $status = 200, bool $error = false): void 
    {
        date_default_timezone_set('America/Guatemala');

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
     * Send an error response
     *
     * @param string $message The error message
     * @param int $status The HTTP status code
     */
    public function error(string $message, int $status = 400): void 
    {
        $this->send(['error' => $message], $status, true);
    }

    /**
     * Send a success response
     *
     * @param mixed $data The data to send in the response
     * @param string $message An optional message
     * @param int $status The HTTP status code
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

/**
 * Request handler class to manage incoming requests
 */
class RequestHandler {
    /**
     * Get JSON data from the request body
     *
     * @return array The decoded JSON data
     */
    public function getJsonData(): array 
    {
        // Normalize input data
        $jsonData = file_get_contents("php://input");
        return json_decode($jsonData, true) ?? [];
    }
}

$response = new ResponseHandler();
$request = new RequestHandler();
$router = new Router();

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

// Tasks Routes
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
    $task = $taskModel->find($id);

    if ($task) {
        $response->success($task);
    } else {
        $response->error("Tarea no encontrada", 404);
    }
});

$router->put('/tasks/{id}', function($id) use ($response, $request) {
    $taskModel = new \App\Models\Task();
    $data = $request->getJsonData();

    if ($taskModel->update($id, $data)) {
        $response->success(null, "Tarea actualizada");
    } else {
        $response->error("No se pudo actualizar la tarea");
    }
});

$router->delete('/tasks/{id}', function($id) use ($response) {
    $taskModel = new \App\Models\Task();

    if ($taskModel->delete($id)) {
        $response->success(null, "Tarea eliminada");
    } else {
        $response->error("No se pudo eliminar la tarea");
    }
});

// Expenses Routes
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
    $expense = $expenseModel->find($id);

    if ($expense) {
        $response->success($expense);
    } else {
        $response->error("Gasto no encontrado", 404);
    }
});

$router->put('/expenses/{id}', function($id) use ($response, $request) {
    $expenseModel = new \App\Models\Expense();
    $data = $request->getJsonData();

    if ($expenseModel->update($id, $data)) {
        $response->success(null, "Gasto actualizado");
    } else {
        $response->error("No se pudo actualizar el gasto");
    }
});

$router->delete('/expenses/{id}', function($id) use ($response) {
    $expenseModel = new \App\Models\Expense();

    if ($expenseModel->delete($id)) {
        $response->success(null, "Gasto eliminado");
    } else {
        $response->error("No se pudo eliminar el gasto");
    }
});

// Incomes Routes
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
    $income = $incomeModel->find($id);

    if ($income) {
        $response->success($income);
    } else {
        $response->error("Ingreso no encontrado", 404);
    }
});

$router->put('/incomes/{id}', function($id) use ($response, $request) {
    $incomeModel = new \App\Models\Income();
    $data = $request->getJsonData();

    if ($incomeModel->update($id, $data)) {
        $response->success(null, "Ingreso actualizado");
    } else {
        $response->error("No se pudo actualizar el ingreso");
    }
});

$router->delete('/incomes/{id}', function($id) use ($response) {
    $incomeModel = new \App\Models\Income();

    if ($incomeModel->delete($id)) {
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
    $response->error($exception->getMessage(), 500);
}