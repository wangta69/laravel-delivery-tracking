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
  protected $signature = 'pondol:install-deliverytracking  {type=full}';

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
    $type = $this->argument('type');
    $this->installLaravelDeliveryTracking($type);

    
  }

  private function installLaravelDeliveryTracking($type) {
    // if ($type == 'full') {
    //   $this->call('pondol:install-common');
    // }

    $this->info(" Install Laravel Delivery Tracking!! ");
    \Artisan::call('vendor:publish',  [
      '--force'=> true,
      '--provider' => 'Pondol\DeliveryTracking\DeliveryTrackingServiceProvider'
    ]);
  }

}
