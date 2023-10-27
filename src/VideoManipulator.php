<?php

namespace DnSoft\Media;

use DnSoft\Media\Models\Media;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Support\Str;

class VideoManipulator
{
  /**
   * Perform the specified dimension on the given media item.
   * @param Media $media
   */
  public function manipulate(Media $media)
  {
    $high = (new X264('aac'))->setKiloBitrate(1000);
    $super = (new X264('aac'))->setKiloBitrate(1500);

    $parentDir = $media->folder ? $media->folder->name : null;
    $directory = $media->getDirectory();
    $filePath = $directory.'/'.$media->name.'.m3u8';
    if ($parentDir) {
      $filePath = $parentDir . $directory.'/'.$media->name.'.m3u8';
    }

    FFMpeg::openUrl([$media->url])
      ->exportForHLS()
      // ->withRotatingEncryptionKey(function($fileName, $content) {
      //   Storage::disk('secrets')->put($fileName, $content);
      // })
      ->addFormat($high, function ($filters) {
        $filters->resize(1280, 720);
      })
      ->addFormat($super, function ($media) {
        $media->addLegacyFilter(function ($filters) {
          $filters->resize(new \FFMpeg\Coordinate\Dimension(2560, 1920));
        });
      })
      ->onProgress(function ($percentage) use ($media) {
        $media->update([
          'processing_percentage' => $percentage
        ]);
      })
      ->toDisk($media->disk)
      ->save($filePath);
    $media->update([
      'processed' => true,
      'processed_file' => $media->name.'.m3u8',
    ]);
  }
}
