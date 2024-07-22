<?php

namespace controllers;

use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ReportController
{
    // Get aggregated hourly report
    public function getHourlyAverages(Request $request, Response $response, $args): Response
    {
        try {
            $result = Capsule::table('hourly_averages')
                ->where('hour', '>=', date('Y-m-d H:i:s', strtotime('-1 week')))
                ->get();

            if ($result->isEmpty()) {
                $response->getBody()->write(json_encode(['message' => 'No hourly averages found.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write($result->toJson());
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to retrieve hourly averages.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }


    // Get malfunctioning report
    public function getMalfunctioningSensors(Request $request, Response $response, $args): Response
    {
        try {
            $result = Capsule::table('malfunctioning_sensors')->get();

            if ($result->isEmpty()) {
                $response->getBody()->write(json_encode(['message' => 'No malfunctioning sensors found.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write($result->toJson());
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to retrieve malfunctioning sensors.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

}
