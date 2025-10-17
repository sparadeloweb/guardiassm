<?php

namespace App\Services\Contracts;

interface DashboardServiceInterface
{
    /**
     * Obtiene las estadísticas del dashboard filtradas por período.
     *
     * @param  string  $period  El período de filtrado (day, week, month, year, total)
     */
    public function getDashboardStats(string $period): array;

    /**
     * Obtiene los datos para los gráficos del dashboard.
     *
     * @param  string  $period  El período de filtrado (day, week, month, year, total)
     */
    public function getChartData(string $period): array;
}
