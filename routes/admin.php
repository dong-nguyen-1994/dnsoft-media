<?php

use DnSoft\Media\Http\Controllers\Admin\FileController;
use DnSoft\Media\Http\Controllers\Admin\FolderController;
use DnSoft\Media\Http\Controllers\Admin\UploadController;
use DnSoft\Media\Http\Controllers\Admin\MediaController;
use DnSoft\Media\Http\Controllers\Admin\TempFileController;

Route::post('media/upload', UploadController::class)
  ->name('media.admin.upload')
  ->middleware('admin.can:media.admin.upload');

Route::prefix('media')->group(function () {
  Route::get('/', function () {
    return view('media::admin.file-manager');
  })->name('media.admin.media.index');

  Route::post('/store', [MediaController::class, 'store'])
    ->name('media.admin.media.store');

  Route::get('{id}/edit', [MediaController::class, 'edit'])
    ->name('media.admin.media.edit');

  Route::put('{id}/update', [MediaController::class, 'update'])
    ->name('media.admin.media.update');

  Route::get('{id}/destroy', [MediaController::class, 'destroy'])
    ->name('media.admin.media.destroy');

  Route::get('/search', [MediaController::class, 'search'])
    ->name('media.admin.media.search');

  Route::get('/sort', [MediaController::class, 'sort'])
    ->name('media.admin.media.sort');

  Route::delete('/delete', [MediaController::class, 'delete'])
    ->name('media.admin.media.delete');

  Route::get('froala-load-images', [MediaController::class, 'froalaLoadImages'])
    ->name('media.admin.media.froala_load_images');

  Route::get('list', [FolderController::class, 'list'])
    ->name('media.admin.folder.list');

  Route::post('create-folder', [FolderController::class, 'create'])
    ->name('media.admin.folder.create');

  Route::get('get-subfolders/{id}', [FolderController::class, 'getSubFolders'])
    ->name('media.admin.folder.get-sub-folder');

  Route::get('get-data-in-folder/{id}', [FolderController::class, 'getDataInSubFolders'])
    ->name('media.admin.folder.get-data-in-folder');

  Route::post('delete-folder', [FolderController::class, 'deleteFolder'])
    ->name('media.admin.folder.delete-folder');

  Route::post('upload', UploadController::class);

  Route::post('update-image-name', [FileController::class, 'rename'])
    ->name('media.admin.file.update-image-name');

  Route::post('update-image-alt', [FileController::class, 'setAlt'])
    ->name('media.admin.file.update-image-alt');

  Route::post('delete-image', [FileController::class, 'delete'])
    ->name('media.admin.file.delete-image');

  Route::post('insert-media-tmp', [TempFileController::class, 'store'])
    ->name('media.admin.file.insert-media-tmp');
  
  Route::post('delete-media-tmp', [TempFileController::class, 'remove'])
    ->name('media.admin.file.delete-media-tmp');
});
