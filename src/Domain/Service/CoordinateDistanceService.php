<?php

namespace Src\Domain\Service;

class CoordinateDistanceService
{
    public const EARTH_RADIUS = 6371;

    public function findClosestBranch(float $lat, float $lon, array $branches): mixed
    {
        $closestBranch = null;
        $minDistance = PHP_FLOAT_MAX; // Una distancia muy grande inicialmente

        foreach ($branches as $branch) {
            $branchLat = $branch['lat'];
            $branchLon = $branch['lon'];

            // Calcular la distancia entre la posición actual y la sucursal
            $distance = $this->haversineDistance($lat, $lon, $branchLat, $branchLon);

            // Si encontramos una sucursal más cercana, actualizamos la distancia mínima
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closestBranch = $branch;
            }
        }

        return $closestBranch;
    }

    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        // Convertir grados a radianes
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Diferencias de latitud y longitud
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        // Fórmula de Haversine
        $a = sin($dLat / 2) * sin($dLat / 2) + cos($lat1) * cos($lat2) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Distancia en kilómetros
        return self::EARTH_RADIUS * $c;
    }
}
