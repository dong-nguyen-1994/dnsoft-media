<?php

namespace DnSoft\Media\Services;

use Illuminate\Support\Facades\Process;

class FileService
{
  /**
   * Get the duration of the video
   */
  public function getVideoDuration($media)
  {
    $path = $media->getFullPath($media->folder);
    $cmd = "ffmpeg -i $path 2>&1 | grep Duration | awk '{print $2}' | tr -d ,";
    $result = Process::forever()->run($cmd);
    $output = $result->output();
    if ($result->successful()) {
      return substr($output, 0, 8);
    }
    return null;
  }

  /**
   * Calculate the total hour of the course
   */
  public function calculateTotalHour($times = [])
  {
    $hh = 0;
    $mm = 0;
    $ss = 0;
    foreach ($times as $time) {
      sscanf($time, '%d:%d:%d', $hours, $mins, $secs);
      $hh += $hours;
      $mm += $mins;
      $ss += $secs;
    }

    $mm += floor($ss / 60);
    $ss = $ss % 60;
    $hh += floor($mm / 60);
    $mm = $mm % 60;
    return sprintf('%02d:%02d:%02d', $hh, $mm, $ss);
  }
}
