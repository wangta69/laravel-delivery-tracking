<?php
namespace Pondol\DeliveryTracking;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

use Pondol\DeliveryTracking\Console\InstallCommand;

class DeliveryTrackingServiceProvider extends ServiceProvider { //  implements DeferrableProvider
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    $this->mergeConfigFrom(
      __DIR__.'/config/courier.php','courier'
    );

    if ($this->app->runningInConsole()) {
      $this->commands([
        InstallCommand::class,
      ]);
    }
  }

  /**
     * Bootstrap any application services.exi
     *
     * @return void
     */
    //public function boot(\Illuminate\Routing\Router $router)
  public function boot(\Illuminate\Routing\Router $router)
  {
    $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    $this->loadViewsFrom(__DIR__.'/resources/views', 'tracking');
  }
}
