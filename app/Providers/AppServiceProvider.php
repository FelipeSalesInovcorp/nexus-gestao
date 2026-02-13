<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\URL;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('local')) {
            URL::forceScheme('https');
        }

        /* Garantir que IP e Device estão sendo salvos (global)
        Activity::saving(function (Activity $activity) {
            $properties = $activity->properties ?? collect();

            $activity->properties = $properties->merge([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });*/

        // Garantir que IP e Device estão sendo salvos (global)
        Activity::saving(function (Activity $activity) {
            // Em CLI/queues nem sempre existe request HTTP real
            if (!app()->runningInConsole() && request()) {
                $props = $activity->properties;

                // properties pode vir como array ou collection
                if (is_array($props)) {
                    $props = collect($props);
                }

                if (!$props) {
                    $props = collect();
                }

                $activity->properties = $props->merge([
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });

        $this->configureDefaults();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
