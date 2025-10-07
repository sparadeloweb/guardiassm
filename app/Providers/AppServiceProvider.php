<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\Contracts\DoctorServiceInterface;
use App\Services\DoctorService;

use App\Repositories\Contracts\DoctorRepositoryInterface;
use App\Repositories\DoctorRepository;

use App\Services\Contracts\ShiftTypeServiceInterface;
use App\Services\ShiftTypeService;

use App\Repositories\Contracts\ShiftTypeRepositoryInterface;
use App\Repositories\ShiftTypeRepository;

use App\Services\Contracts\PatientServiceInterface;
use App\Services\PatientService;

use App\Repositories\Contracts\PatientRepositoryInterface;
use App\Repositories\PatientRepository;

use App\Services\Contracts\PathologyServiceInterface;
use App\Services\PathologyService;

use App\Repositories\Contracts\PathologyRepositoryInterface;
use App\Repositories\PathologyRepository;

use App\Services\Contracts\ShiftServiceInterface;
use App\Services\ShiftService;

use App\Repositories\Contracts\ShiftRepositoryInterface;
use App\Repositories\ShiftRepository;

use App\Repositories\Contracts\AttentionRepositoryInterface;
use App\Repositories\AttentionRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DoctorServiceInterface::class, DoctorService::class);
        $this->app->bind(DoctorRepositoryInterface::class, DoctorRepository::class);

        $this->app->bind(ShiftTypeServiceInterface::class, ShiftTypeService::class);
        $this->app->bind(ShiftTypeRepositoryInterface::class, ShiftTypeRepository::class);

        $this->app->bind(PatientServiceInterface::class, PatientService::class);
        $this->app->bind(PatientRepositoryInterface::class, PatientRepository::class);

        $this->app->bind(PathologyServiceInterface::class, PathologyService::class);
        $this->app->bind(PathologyRepositoryInterface::class, PathologyRepository::class);

        $this->app->bind(ShiftServiceInterface::class, ShiftService::class);
        $this->app->bind(ShiftRepositoryInterface::class, ShiftRepository::class);
        $this->app->bind(AttentionRepositoryInterface::class, AttentionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
