<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Contracts\DashboardServiceInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    private $dashboardService;

    public function __construct(DashboardServiceInterface $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Muestra el panel de control.
     *
     */
    public function index(Request $request): Response
    {
        $period = $request->get('period', 'total');

        $stats = $this->dashboardService->getDashboardStats($period);

        $chartData = $this->dashboardService->getChartData($period);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'chartData' => $chartData,
            'period' => $period,
        ]);
    }
}
