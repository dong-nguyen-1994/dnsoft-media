<?php

namespace DnSoft\Media\Console\Commands;

use DnSoft\Media\Models\Media;
use DnSoft\Media\VideoManipulator;
use Illuminate\Console\Command;

class VideoEncode extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'dnsoft:video-encode {media}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command encode video';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $mediaId = $this->argument('media');
    /** @var Media */
    $media = Media::find($mediaId);
    $this->info('Starting video manipulation');
    app(VideoManipulator::class)->manipulate($media);
    $this->info('Finished manipulation video');
  }
}
