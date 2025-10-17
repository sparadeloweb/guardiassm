<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface DashboardRepositoryInterface
{
    /**
     * Obtiene query builder de guardias filtrado por período.
     *
     * @param  string  $period  El período de filtrado (day, week, month, year, total)
     */
    public function getShiftsQueryByPeriod(string $period): Builder;

    /**
     * Obtiene query builder de atenciones filtrado por período.
     *
     * @param  string  $period  El período de filtrado (day, week, month, year, total)
     */
    public function getAttentionsQueryByPeriod(string $period): Builder;

    /**
     * Obtiene el conteo de horarios por hora del día.
     */
    public function getPeakHoursData(Builder $attentionsQuery): array;

    /**
     * Obtiene las patologías más frecuentes.
     */
    public function getTopPathologiesData(Builder $attentionsQuery): array;

    /**
     * Obtiene los doctores con más guardias.
     */
    public function getTopDoctorsData(Builder $shiftsQuery): array;

    /**
     * Obtiene la evolución mensual de guardias según período.
     *
     * @param  string  $period  El período de filtrado (day, week, month, year, total)
     */
    public function getMonthlyEvolutionData(string $period): array;
}
