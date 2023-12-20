<?php

namespace DnSoft\Media\Jobs;

use DnSoft\Media\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;

class HandleVideoUploaded implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /** @var Media */
  protected $media;

  /**
   * Create a new job instance.
   */
  public function __construct(Media $media)
  {
    $this->media = $media;
  }

  /** @return Media */
  public function getMedia()
  {
    return $this->media;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $mediaId = $this->getMedia()->id;
    if ($mediaId) {
      $cmd = "php artisan dnsoft:video-encode $mediaId";
      Process::start($cmd);
    }
  }
}
