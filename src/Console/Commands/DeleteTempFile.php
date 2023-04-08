<?php

namespace DnSoft\Media\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DeleteTempFile extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'dnsoft:delete-temp-file';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command delete the file in table media__temp_media for records has created greater than 2h';

  /**
   * Execute the console command.
   */
  public function handle(): void
  {
    $this->info('*********** STARTING DELETE *************');
    DB::table('media__media_temps')->where('created_at', '<', Carbon::parse('-1 hours'))->delete();
    $this->info('*********** ENDED DELETE *************');
  }
}
