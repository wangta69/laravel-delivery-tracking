<?php

namespace Pondol\DeliveryTracking\Console;

use Illuminate\Console\Command;
// use Illuminate\Filesystem\Filesystem;
// use Illuminate\Support\Str;
// use Symfony\Component\Process\PhpExecutableFinder;
// use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
  // use InstallsBladeStack;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'deliverytracking:install';

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
