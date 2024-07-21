<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Routing\RouteContext;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

// Require autoload file
require __DIR__ . '/../vendor/autoload.php';

// Create App
$app = AppFactory::create();

// Middleware
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

// CORS Middleware
$app->add(function (Request $request, RequestHandler $handler): Response {
    if ($request->getMethod() === "OPTIONS") {
        $response = new SlimResponse();
        return $response->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withStatus(200);
    }

    $response = $handler->handle($request);

    return $response->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true');
});

// Error Handling
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Database Connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'db',
    'database' => 'a_tower',
    'username' => 'user',
    'password' => 'password',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Load Controllers
use App\Controllers\SensorController;
use App\Controllers\SensorDataController;
use App\Controllers\ReportController;

$sensorController = new SensorController();
$sensorDataController = new SensorDataController();
$reportController = new ReportController();

// Define Routes
$app->get('/sensors', [$sensorController, 'getAllSensors']);
$app->post('/add-100-sensors', [$sensorController, 'add100Sensors']);
$app->post('/add-sensor', [$sensorController, 'addSensor']);
$app->post('/set-sensor-data/{id}', [$sensorDataController, 'setSensorData']);
$app->get('/get-sensor-data', [$sensorDataController, 'getSensorData']);
$app->get('/reports/hourly-averages', [$reportController, 'getHourlyAverages']);
$app->get('/reports/malfunctioning-sensors', [$reportController, 'getMalfunctioningSensors']);
$app->delete('/delete-sensor/{id}', [$sensorController, 'deleteSensor']);

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();