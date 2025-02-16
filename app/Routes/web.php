<?php

use App\Core\Router;
use App\Handlers\JsonHandler;
use App\Exceptions\Handler;

/**
 * Envía la respuesta en formato JSON con información adicional
 *
 * @param mixed $data    Datos a enviar
 * @param int   $status  Código de estado HTTP
 * @param bool  $error   Indica si es una respuesta de error
 */
function sendResponse($data, $status = 200, $error = false) {
    date_default_timezone_set('America/Guatemala'); // TODO: implementar esto dinamicamente para varios paises

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
 * Obtiene y decodifica los datos JSON del request
 *
 * @return array
 */
function getRequestData() {
    $jsonData = file_get_contents("php://input");
    return json_decode($jsonData, true) ?? [];
}

$router = new Router();

$apiMetadata = [
    'name' => 'API David',
    'version' => '1.0.0',
    'endpoints' => [
        'tasks'    => '/tasks',
        'expenses' => '/expenses',
        'incomes'  => '/incomes'
    ]
];

$router->get('/', function () use ($apiMetadata) {
    sendResponse($apiMetadata);
});

// Rutas de Tasks
$router->get('/tasks', function () {
    $taskModel = new \App\Models\Task();
    sendResponse($taskModel->all());
});

$router->post('/tasks', function () {
    $taskModel = new \App\Models\Task();
    $data = getRequestData();
    
    if ($taskModel->create($data)) {
        sendResponse(["message" => "Tarea creada"], 201);
    } else {
        sendResponse(["error" => "No se pudo crear la tarea"], 400, true);
    }
});

$router->get('/tasks/{id}', function ($id) {
    $taskModel = new \App\Models\Task();
    $task = $taskModel->find($id);
    
    if ($task) {
        sendResponse($task);
    } else {
        sendResponse(["error" => "Tarea no encontrada"], 404, true);
    }
});

$router->put('/tasks/{id}', function ($id) {
    $taskModel = new \App\Models\Task();
    $data = getRequestData();
    
    if ($taskModel->update($id, $data)) {
        sendResponse(["message" => "Tarea actualizada"]);
    } else {
        sendResponse(["error" => "No se pudo actualizar la tarea"], 400, true);
    }
});

$router->delete('/tasks/{id}', function ($id) {
    $taskModel = new \App\Models\Task();
    
    if ($taskModel->delete($id)) {
        sendResponse(["message" => "Tarea eliminada"]);
    } else {
        sendResponse(["error" => "No se pudo eliminar la tarea"], 400, true);
    }
});

// Rutas de Expenses
$router->get('/expenses', function () {
    $expenseModel = new \App\Models\Expense();
    sendResponse($expenseModel->all());
});

$router->post('/expenses', function () {
    $expenseModel = new \App\Models\Expense();
    $data = getRequestData();
    
    if ($expenseModel->create($data)) {
        sendResponse(["message" => "Gasto registrado"], 201);
    } else {
        sendResponse(["error" => "No se pudo registrar el gasto"], 400, true);
    }
});

$router->get('/expenses/{id}', function ($id) {
    $expenseModel = new \App\Models\Expense();
    $expense = $expenseModel->find($id);
    
    if ($expense) {
        sendResponse($expense);
    } else {
        sendResponse(["error" => "Gasto no encontrado"], 404, true);
    }
});

$router->put('/expenses/{id}', function ($id) {
    $expenseModel = new \App\Models\Expense();
    $data = getRequestData();
    
    if ($expenseModel->update($id, $data)) {
        sendResponse(["message" => "Gasto actualizado"]);
    } else {
        sendResponse(["error" => "No se pudo actualizar el gasto"], 400, true);
    }
});

$router->delete('/expenses/{id}', function ($id) {
    $expenseModel = new \App\Models\Expense();
    
    if ($expenseModel->delete($id)) {
        sendResponse(["message" => "Gasto eliminado"]);
    } else {
        sendResponse(["error" => "No se pudo eliminar el gasto"], 400, true);
    }
});

// Rutas de Incomes
$router->get('/incomes', function () {
    $incomeModel = new \App\Models\Income();
    sendResponse($incomeModel->all());
});

$router->post('/incomes', function () {
    $incomeModel = new \App\Models\Income();
    $data = getRequestData();
    
    if ($incomeModel->create($data)) {
        sendResponse(["message" => "Ingreso registrado"], 201);
    } else {
        sendResponse(["error" => "No se pudo registrar el ingreso"], 400, true);
    }
});

$router->get('/incomes/{id}', function ($id) {
    $incomeModel = new \App\Models\Income();
    $income = $incomeModel->find($id);
    
    if ($income) {
        sendResponse($income);
    } else {
        sendResponse(["error" => "Ingreso no encontrado"], 404, true);
    }
});

$router->put('/incomes/{id}', function ($id) {
    $incomeModel = new \App\Models\Income();
    $data = getRequestData();
    
    if ($incomeModel->update($id, $data)) {
        sendResponse(["message" => "Ingreso actualizado"]);
    } else {
        sendResponse(["error" => "No se pudo actualizar el ingreso"], 400, true);
    }
});

$router->delete('/incomes/{id}', function ($id) {
    $incomeModel = new \App\Models\Income();
    
    if ($incomeModel->delete($id)) {
        sendResponse(["message" => "Ingreso eliminado"]);
    } else {
        sendResponse(["error" => "No se pudo eliminar el ingreso"], 400, true);
    }
});

try {
    $router->run();
} catch (\Throwable $exception) {
    $errorHandler = new Handler();
    // Internal Server Error (500) por defecto
    echo $errorHandler->showError(500, $exception->getMessage());
}
