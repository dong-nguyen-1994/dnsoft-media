<?php

namespace DnSoft\Media\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DnSoft\Media\ImageManipulator;
use DnSoft\Media\Models\Folder;
use DnSoft\Media\Models\Media;

class PerformConversions implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /** @var Media */
  protected $media;

  /** @var array */
  protected $conversions;

  /** @var Folder */
  protected $selectedFolder;

  /**
   * Create a new job instance.
   *
   * @param Media $media
   * @param array $conversions
   * @return void
   */
  public function __construct(Media $media, Folder $selectedFolder, array $conversions)
  {
    $this->media = $media;
    $this->selectedFolder = $selectedFolder;
    $this->conversions = $conversions;
    $this->queue = config('media.queue');
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    app(ImageManipulator::class)->manipulate(
      $this->media,
      $this->selectedFolder,
      $this->conversions
    );
  }

  /** @return Media */
  public function getMedia()
  {
    return $this->media;
  }

  /** @return array */
  public function getConversions()
  {
    return $this->conversions;
  }
}
