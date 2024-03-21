<?php

namespace App\Providers;

use App\Enums\BannerTypeEnum;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // @codeCoverageIgnoreStart
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }
        // @codeCoverageIgnoreEnd

        RedirectResponse::macro('withBanner', function (string $message, ?BannerTypeEnum $bannerType = BannerTypeEnum::success) {
            // @phpstan-ignore-next-line
            return $this->with('flash', [
                'bannerStyle' => $bannerType,
                'banner' => $message,
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
