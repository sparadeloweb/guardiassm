<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Pathology;
use App\Models\Patient;
use App\Models\Shift;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Services\Contracts\DashboardServiceInterface;
use Illuminate\Support\Facades\Log;

class DashboardService implements DashboardServiceInterface
{
    private $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    /**
     * Obtiene las estadísticas del dashboard filtradas por período.
     *
     * @param  string  $period  El período de filtrado (day, week, month, year, total)
     */
    public function getDashboardStats(string $period): array
    {
        Log::info('Dashboard period filter:', ['period' => $period]);

        // Estadísticas básicas (sin filtrar por período)
        $stats = [
            'doctors_count' => Doctor::count(),
            'patients_count' => Patient::count(),
            'pathologies_count' => Pathology::count(),
        ];

        // Obtener queries filtrados por período
        $shiftsQuery = $this->dashboardRepository->getShiftsQueryByPeriod($period);
        $attentionsQuery = $this->dashboardRepository->getAttentionsQueryByPeriod($period);

        // Estadísticas filtradas por período
        $stats['shifts_count'] = $shiftsQuery->count();
        $stats['attentions_count'] = $attentionsQuery->count();
        $stats['total_revenue'] = $shiftsQuery->sum('total_amount');
        $stats['unpaid_shifts'] = Shift::whereNull('paid_at')->count();
        $stats['paid_shifts'] = Shift::whereNotNull('paid_at')->count();

        Log::info('Dashboard stats:', [
            'shifts_count' => $stats['shifts_count'],
            'attentions_count' => $stats['attentions_count'],
            'total_revenue' => $stats['total_revenue'],
            'period' => $period,
        ]);

        return $stats;
    }

    /**
     * Obtiene los datos para los gráficos del dashboard.
     *
     * @param  string  $period  El período de filtrado (day, week, month, year, total)
     */
    public function getChartData(string $period): array
    {
        // Obtener queries filtrados por período
        $shiftsQuery = $this->dashboardRepository->getShiftsQueryByPeriod($period);
        $attentionsQuery = $this->dashboardRepository->getAttentionsQueryByPeriod($period);

        return [
            // Horarios pico de atención (por hora del día)
            'peak_hours' => $this->dashboardRepository->getPeakHoursData(clone $attentionsQuery),

            // Patologías más frecuentes
            'top_pathologies' => $this->dashboardRepository->getTopPathologiesData(clone $attentionsQuery),

            // Doctores con más guardias y horas
            'top_doctors' => $this->dashboardRepository->getTopDoctorsData(clone $shiftsQuery),

            // Evolución mensual de atenciones (según período seleccionado)
            'monthly_evolution' => $this->dashboardRepository->getMonthlyEvolutionData($period),
        ];
    }
}
