<?php

use DnSoft\Media\Http\Controllers\Api\VideoController;
Route::middleware('auth.sanctum')->group(function() {
  Route::get('/videos/secret/{key}', [VideoController::class, 'getVideoSecret'])->name('web.media.video.key');
  Route::get('/videos/{playlist}', [VideoController::class, 'playVideo'])->name('web.media.video.playlist');
  Route::get('videos/progress/{fileId}', [VideoController::class, 'getHandleProgress'])->name('api.media.video.progress');
});
