<?php

declare(strict_types=1);

namespace App\Providers;

use App\Gateway\Middleware\GatewayCorrelationIdMiddleware;
use App\Shared\Controllers\TenantController;
use App\Shared\Middleware\TenantResolutionMiddleware;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * List of microservice modules.
     *
     * @var array<int, string>
     */
    protected array $modules = [
        'Identity',
        'Attendance',
        'Payroll',
        'Accounting',
        'Inventory',
        'Notification',
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind module services into container if needed
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerModuleRoutes();
    }

    /**
     * Register routes for each microservice module.
     */
    protected function registerModuleRoutes(): void
    {
        $modulesPath = app_path('Modules');

        if (! File::isDirectory($modulesPath)) {
            return;
        }

        $commonMiddleware = [
            'api',
            GatewayCorrelationIdMiddleware::class,
            TenantResolutionMiddleware::class,
        ];

        // Register routes for individual microservice modules with Tenant & Gateway middleware
        foreach ($this->modules as $module) {
            $routePath = $modulesPath.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.'routes.php';

            if (File::exists($routePath)) {
                $prefix = 'api/'.Str::kebab($module);
                $as = Str::kebab($module).'.';

                Route::middleware($commonMiddleware)
                    ->prefix($prefix)
                    ->as($as)
                    ->group($routePath);
            }
        }

        // Register API Gateway routes
        $gatewayRoutePath = app_path('Gateway'.DIRECTORY_SEPARATOR.'routes.php');
        if (File::exists($gatewayRoutePath)) {
            Route::middleware($commonMiddleware)
                ->prefix('api/gateway')
                ->as('gateway.')
                ->group($gatewayRoutePath);
        }

        // SaaS Tenant Management Routes (Landlord layer)
        Route::middleware($commonMiddleware)
            ->prefix('api/tenants')
            ->as('tenants.')
            ->group(function (): void {
                Route::get('/', [TenantController::class, 'index'])->name('index');
                Route::get('/current', [TenantController::class, 'current'])->name('current');
                Route::post('/', [TenantController::class, 'store'])->name('store');
            });

        // Global microservice discovery / health overview route
        Route::middleware($commonMiddleware)
            ->prefix('api')
            ->get('/modules', function () {
                return response()->json([
                    'system' => config('app.name', 'Laravel Microservice Hub'),
                    'status' => 'operational',
                    'timestamp' => now()->toIso8601String(),
                    'modules' => collect($this->modules)->mapWithKeys(function (string $module) {
                        return [
                            Str::kebab($module) => [
                                'name' => $module,
                                'endpoint' => url('api/'.Str::kebab($module)),
                                'status' => 'active',
                            ],
                        ];
                    }),
                ]);
            })
            ->name('api.modules.index');
    }
}
