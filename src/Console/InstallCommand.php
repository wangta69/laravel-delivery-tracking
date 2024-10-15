<?php

namespace Pondol\DeliveryTracking\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
  // use InstallsBladeStack;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'pondol:install-deliverytracking';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Install the delivery tracking system and resources';


  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {

    $this->info(" Install Laravel Delivery Tracking ");
    \Artisan::call('vendor:publish',  [
      '--force'=> true,
      '--provider' => 'Pondol\DeliveryTrackingphp\DeliveryTrackingServiceProvider'
    ]);
  }

}
