<?php
namespace Pondol\DeliveryTracking;

// use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Pondol\Market\Console\InstallCommand;
use Illuminate\Support\Facades\Route;
class DeliveryTrackingServiceProvider extends ServiceProvider { //  implements DeferrableProvider
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
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





  }


}
