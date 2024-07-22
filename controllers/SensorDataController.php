<?php

namespace controllers;

use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SensorDataController
{
    // Set sensor data for check
    public function setSensorData(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $sensorId = $args['id'];
        $sensor = Capsule::table('sensors')->where('id', $sensorId)->first();

        if (!$sensor) {
            $response->getBody()->write(json_encode(['message' => 'Sensor with id [' . $sensorId . '] not found.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        Capsule::table('sensor_data')->insert([
            'timestamp' => date('Y-m-d H:i:s'),
            'sensor_id' => $sensor->id,
            'face' => $sensor->face,
            'temperature' => $data['temperature']
        ]);
        $response->getBody()->write(json_encode(['message' => 'Sensor data was recorded successfully.']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Get sensor data for check
    public function getSensorData(Request $request, Response $response)
    {
        $result = Capsule::table('sensor_data')->get();
        $response->getBody()->write($result->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }
}
