<?php

namespace App\Repositories;

use App\Models\Attention;
use App\Models\Shift;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DashboardRepository implements DashboardRepositoryInterface
{
    private $shift;

    private $attention;

    public function __construct(Shift $shift, Attention $attention)
    {
        $this->shift = $shift;
        $this->attention = $attention;
    }

    /**
     * Obtiene query builder de guardias filtrado por período.
     *
     * @param  string  $period  El período de filtrado (day, week, month, year, total)
     */
    public function getShiftsQueryByPeriod(string $period): Builder
    {
        $query = $this->shift->query();

        switch ($period) {
            case 'day':
                $query->whereDate('starts_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('starts_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('starts_at', Carbon::now()->month)
                    ->whereYear('starts_at', Carbon::now()->year);
                break;
            case 'year':
                $query->whereYear('starts_at', Carbon::now()->year);
                break;
            case 'total':
            default:
                // No aplicar filtros adicionales
                break;
        }

        return $query;
    }

    /**
     * Obtiene query builder de atenciones filtrado por período.
     *
     * @param  string  $period  El período de filtrado (day, week, month, year, total)
     */
    public function getAttentionsQueryByPeriod(string $period): Builder
    {
        $query = $this->attention->query();

        switch ($period) {
            case 'day':
                $query->whereDate('attended_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('attended_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('attended_at', Carbon::now()->month)
                    ->whereYear('attended_at', Carbon::now()->year);
                break;
            case 'year':
                $query->whereYear('attended_at', Carbon::now()->year);
                break;
            case 'total':
            default:
                // No aplicar filtros adicionales
                break;
        }

        return $query;
    }

    /**
     * Obtiene el conteo de horarios por hora del día.
     */
    public function getPeakHoursData(Builder $attentionsQuery): array
    {
        $attentions = $attentionsQuery->get();
        $hourlyCounts = [];

        // Inicializar todas las horas con 0
        for ($i = 0; $i < 24; $i++) {
            $hourlyCounts[$i] = 0;
        }

        // Contar atenciones por hora
        foreach ($attentions as $attention) {
            $hour = Carbon::parse($attention->attended_at)->hour;
            $hourlyCounts[$hour]++;
        }

        return [
            'labels' => array_map(fn ($h) => sprintf('%02d:00', $h), array_keys($hourlyCounts)),
            'data' => array_values($hourlyCounts),
        ];
    }

    /**
     * Obtiene las patologías más frecuentes.
     */
    public function getTopPathologiesData(Builder $attentionsQuery): array
    {
        $attentions = $attentionsQuery->with('pathologies')->get();
        $pathologyCounts = [];

        foreach ($attentions as $attention) {
            foreach ($attention->pathologies as $pathology) {
                if (! isset($pathologyCounts[$pathology->id])) {
                    $pathologyCounts[$pathology->id] = [
                        'name' => $pathology->name,
                        'count' => 0,
                    ];
                }
                $pathologyCounts[$pathology->id]['count']++;
            }
        }

        // Ordenar por cantidad y tomar los top 5
        uasort($pathologyCounts, fn ($a, $b) => $b['count'] <=> $a['count']);
        $topPathologies = array_slice($pathologyCounts, 0, 5, true);

        return [
            'labels' => array_column($topPathologies, 'name'),
            'data' => array_column($topPathologies, 'count'),
        ];
    }

    /**
     * Obtiene los doctores con más guardias.
     */
    public function getTopDoctorsData(Builder $shiftsQuery): array
    {
        $shifts = $shiftsQuery->with('doctor')->get();
        $doctorStats = [];

        foreach ($shifts as $shift) {
            if ($shift->doctor) {
                $doctorId = $shift->doctor->id;
                if (! isset($doctorStats[$doctorId])) {
                    $doctorStats[$doctorId] = [
                        'name' => $shift->doctor->name,
                        'shifts_count' => 0,
                        'total_hours' => 0,
                    ];
                }
                $doctorStats[$doctorId]['shifts_count']++;
                $doctorStats[$doctorId]['total_hours'] += $shift->total_hours;
            }
        }

        // Ordenar por cantidad de guardias y tomar los top 5
        uasort($doctorStats, fn ($a, $b) => $b['shifts_count'] <=> $a['shifts_count']);
        $topDoctors = array_slice($doctorStats, 0, 5, true);

        return [
            'labels' => array_column($topDoctors, 'name'),
            'shifts_data' => array_column($topDoctors, 'shifts_count'),
            'hours_data' => array_column($topDoctors, 'total_hours'),
        ];
    }

    /**
     * Obtiene la evolución mensual de atenciones según período.
     *
     * @param  string  $period  El período de filtrado (day, week, month, year, total)
     */
    public function getMonthlyEvolutionData(string $period): array
    {
        $months = [];
        $attentionsData = [];

        if ($period === 'day') {
            // Para "hoy", mostrar las últimas 24 horas
            for ($i = 23; $i >= 0; $i--) {
                $hour = Carbon::now()->subHours($i);
                $months[] = $hour->format('H:00');

                $hourAttentions = $this->attention
                    ->whereRaw('HOUR(attended_at) = ?', [$hour->hour])
                    ->whereDate('attended_at', $hour->toDateString())
                    ->count();
                $attentionsData[] = $hourAttentions;
            }
        } elseif ($period === 'week') {
            // Para "esta semana", mostrar los últimos 7 días
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $months[] = $date->format('D d');

                $dayAttentions = $this->attention
                    ->whereDate('attended_at', $date->toDateString())
                    ->count();
                $attentionsData[] = $dayAttentions;
            }
        } elseif ($period === 'month') {
            // Para "este mes", mostrar las últimas 4 semanas
            for ($i = 3; $i >= 0; $i--) {
                $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                $weekEnd = $weekStart->copy()->endOfWeek();
                $months[] = 'Sem '.(4 - $i);

                $weekAttentions = $this->attention
                    ->whereBetween('attended_at', [$weekStart, $weekEnd])
                    ->count();
                $attentionsData[] = $weekAttentions;
            }
        } elseif ($period === 'year') {
            // Para "este año", mostrar los últimos 12 meses
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $months[] = $date->format('M');

                $monthAttentions = $this->attention
                    ->whereMonth('attended_at', $date->month)
                    ->whereYear('attended_at', $date->year)
                    ->count();
                $attentionsData[] = $monthAttentions;
            }
        } else {
            // Para "total", mostrar los últimos 6 meses
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $months[] = $date->format('M Y');

                $monthAttentions = $this->attention
                    ->whereMonth('attended_at', $date->month)
                    ->whereYear('attended_at', $date->year)
                    ->count();
                $attentionsData[] = $monthAttentions;
            }
        }

        return [
            'labels' => $months,
            'data' => $attentionsData,
        ];
    }
}
