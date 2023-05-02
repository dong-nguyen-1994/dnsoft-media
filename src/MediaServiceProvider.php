<?php

namespace DnSoft\Media;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Image;
use DnSoft\Acl\Facades\Permission;
use DnSoft\Core\Events\CoreAdminMenuRegistered;
use DnSoft\Media\Console\Commands\DeleteTempFile;
use DnSoft\Media\Events\MediaUploadedEvent;
use DnSoft\Media\Facades\Conversion;
use DnSoft\Media\Jobs\PerformConversions;
use DnSoft\Media\Models\Media;
use DnSoft\Media\Models\Mediable;
use DnSoft\Media\Models\MediaTag;
use DnSoft\Media\Repositories\MediableRepository;
use DnSoft\Media\Repositories\MediableRepositoryInterace;
use DnSoft\Media\Repositories\MediaRepository;
use DnSoft\Media\Repositories\MediaRepositoryInterface;
use DnSoft\Media\Repositories\MediaTagRepositoryInterface;

class MediaServiceProvider extends ServiceProvider
{
  protected $commandsCLI = [
    DeleteTempFile::class
  ];

  public function register()
  {
    $this->mergeConfigFrom(
      __DIR__ . '/../config/media.php',
      'media'
    );

    $this->app->singleton(ConversionRegistry::class);
    $this->app->singleton(MediaUploader::class);

    $this->app->singleton(MediaRepositoryInterface::class, function () {
      return new MediaRepository(new Media());
    });

    $this->app->singleton(MediableRepositoryInterace::class, function () {
      return new MediableRepository(new Mediable());
    });
    $this->app->singleton(MediaTagRepositoryInterface::class, function () {
      return new MediaRepository(new MediaTag());
    });
  }

  public function boot()
  {
    $this->publishes([
      __DIR__ . '/../config/media.php' => config_path('media.php'),
    ], 'config');

    $this->publishes([
      __DIR__ . '/../public/v1' => public_path('vendor/media/v1'),
    ], 'dnsoft-media-v1');

    $this->publishes([
      __DIR__ . '/../public/v2' => public_path('vendor/media/v2'),
    ], 'dnsoft-media-v2');

    $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

    $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'media');

    $this->loadViewsFrom(__DIR__ . '/../resources/views', 'media');

    $this->registerDefaultConversion();

    $this->loadRoutes();

    $this->registerPermissions();

    $this->registerAdminMenus();

    $this->registerBlade();

    $this->registerCommands();
  }

  protected function registerDefaultConversion()
  {
    $thumbSize = config('media.thumbsize', []);
    if ($thumbSize && count($thumbSize) == 2) {
      Conversion::register('thumb', function (Image $image) use ($thumbSize) {
        return $image->fit($thumbSize[0], $thumbSize[1]);
      });

      Event::listen(MediaUploadedEvent::class, function (MediaUploadedEvent $event) {
        PerformConversions::dispatch(
          $event->media,
          $event->selectedFolder,
          ['thumb']
        );
      });
    }
  }

  protected function loadRoutes()
  {
    Route::middleware(['web', 'admin.auth'])
      ->prefix('admin')
      ->group(__DIR__ . '/../routes/admin.php');

    //        Route::middleware(['web'])
    //            ->group(__DIR__.'/../routes/web.php');

    //        Route::middleware(['web'])->group('./../routes/web.php');
  }

  protected function registerPermissions()
  {
    Permission::add('media.admin.index', __('media::permission.media.index'));
    Permission::add('media.admin.upload', __('media::permission.media.upload'));
  }

  private function registerAdminMenus()
  {
    Event::listen(CoreAdminMenuRegistered::class, function ($menu) {
      // $menu->add(__('Media'), [
      //   'route' => 'media.admin.media.index',
      // ])->data('order', 12000)->data('icon', 'fa fa-image');
    });
  }

  public function registerBlade()
  {
    $version = get_version_actived();
    Blade::include("media::$version.admin.modals.tag-modal", 'modalMedia');
    Blade::include("media::$version.admin.forms.file-manager", 'fileManager');
    Blade::include("media::$version.admin.forms.single-file", 'singleFile');
    Blade::include("media::$version.admin.forms.single-image", 'singleImage');
    Blade::include("media::$version.admin.forms.media-v2", 'mediaV2');
    Blade::include("media::v1.admin.forms.media-v1", 'mediaV1');
    Blade::include("media::v1.admin.forms.gallery-v1", 'galleryV1');
  }

  public function registerCommands()
  {
    if ($this->app->runningInConsole()) {
      $this->commands($this->commandsCLI);
    }
  }
}
