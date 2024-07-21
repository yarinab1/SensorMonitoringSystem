<?php

namespace App\Controllers;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SensorController
{
    // Get all sensors
    public function getAllSensors(Request $request, Response $response, $args): Response
    {
        try {
            // Retrieve the sensors data
            $result = Capsule::table('sensors')->get();

            // Check if the result is empty
            if ($result->isEmpty()) {
                $response->getBody()->write(json_encode(['message' => 'No sensors found.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write($result->toJson());
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to retrieve sensors.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // Add 100 sensors for simulation
    public function add100Sensors(Request $request, Response $response): Response
    {
        // Maximum allowed sensors
        $maxSensors = 10000;

        // Check current sensor count
        $currentCount = Capsule::table('sensors')->count();

        if ($currentCount >= $maxSensors) {
            $response->getBody()->write(json_encode(['error' => 'No place for sensors.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Determine how many sensors we can add
        $availableSpace = $maxSensors - $currentCount;
        $sensorsToAdd = min(100, $availableSpace);

        $faces = ['north', 'east', 'south', 'west'];

        // Create an array to hold sensor data
        $sensorsData = [];
        for ($i = 0; $i < $sensorsToAdd; $i++) {
            $key = array_rand($faces);
            $sensorsData[] = [
                'face' => $faces[$key],
                'isOn' => false
            ];
        }

        // Use transaction to ensure atomicity
        DB::transaction(function () use ($sensorsData) {
            Capsule::table('sensors')->insert($sensorsData);
        });

        $response->getBody()->write(json_encode(['message' => 'Sensors added successfully.']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // Add 1 sensor from payload
    public function addSensor(Request $request, Response $response, $args): Response
    {
        // Define the maximum allowed sensors
        $maxSensors = 10000;

        // Check the current sensor count
        $currentCount = Capsule::table('sensors')->count();

        if ($currentCount >= $maxSensors) {
            $response->getBody()->write(json_encode(['error' => 'No place for sensor.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Parse input data
        $data = $request->getParsedBody();
        $face = $data['face'] ?? null;

        // Validate the input data
        if (is_null($face) || !in_array($face, ['north', 'east', 'south', 'west'])) {
            $response->getBody()->write(json_encode(['error' => 'Invalid sensor face.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            // Use transaction to ensure atomicity
            DB::transaction(function () use ($face) {
                Capsule::table('sensors')->insert([
                    'face' => $face,
                    'isOn' => false
                ]);
            });

            $response->getBody()->write(json_encode(['message' => 'Sensor added successfully.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to add sensor.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }


    // Delete sensor by id
    public function deleteSensor(Request $request, Response $response, $args): Response
    {
        $sensorId = $args['id'];

        // Validate the sensor ID
        if (!is_numeric($sensorId)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid sensor ID.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            // Use a transaction to ensure all deletions are atomic
            DB::transaction(function () use ($sensorId) {
                // Delete sensor data
                Capsule::table('sensor_data')->where('sensor_id', $sensorId)->delete();

                // Delete malfunctioning sensors
                Capsule::table('malfunctioning_sensors')->where('sensor_id', $sensorId)->delete();

                // Delete the sensor
                Capsule::table('sensors')->where('id', $sensorId)->delete();
            });

            $response->getBody()->write(json_encode(['message' => 'The sensor with id [' . $sensorId . '] has been deleted successfully.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to delete sensor.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

}
