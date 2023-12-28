<?php

namespace DnSoft\Media;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Image;
use DnSoft\Acl\Facades\Permission;
use DnSoft\Core\Events\CoreAdminMenuRegistered;
use DnSoft\Core\Support\BaseModuleServiceProvider;
use DnSoft\Media\Console\Commands\VideoEncode;
use DnSoft\Media\Events\GetVideoInformationEvent;
use DnSoft\Media\Events\MediaUploadedEvent;
use DnSoft\Media\Facades\Conversion;
use DnSoft\Media\Interface\FolderInterface;
use DnSoft\Media\Jobs\HandleVideoUploaded;
use DnSoft\Media\Jobs\PerformConversions;
use DnSoft\Media\Listeners\GetVideoInformationListener;
use DnSoft\Media\Models\Media;
use DnSoft\Media\Models\Mediable;
use DnSoft\Media\Models\MediaTag;
use DnSoft\Media\Repositories\MediableRepository;
use DnSoft\Media\Repositories\MediableRepositoryInterace;
use DnSoft\Media\Repositories\MediaRepository;
use DnSoft\Media\Repositories\MediaRepositoryInterface;
use DnSoft\Media\Repositories\MediaTagRepositoryInterface;
use DnSoft\Media\Services\FolderService;

class MediaServiceProvider extends BaseModuleServiceProvider
{
  protected $commandsCLI = [
    VideoEncode::class
  ];

  public function getModuleNamespace()
  {
    return 'media';
  }

  public function register()
  {
    parent::register();
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

    $this->app->singleton(FolderInterface::class, FolderService::class);
  }

  public function boot()
  {
    parent::boot();
    $this->publishes([
      __DIR__ . '/../config/media.php' => config_path('media.php'),
    ], 'config');

    $this->publishes([
      __DIR__ . '/../public' => public_path('vendor/media'),
    ], 'dnsoft-media');

    $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

    $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'media');

    $this->loadViewsFrom(__DIR__ . '/../resources/views', 'media');

    $this->registerDefaultConversion();

    $this->loadRoutes();

    $this->registerPermissions();

    $this->registerAdminMenus();

    $this->registerBlade();

    $this->registerCommands();

    $this->registerEvents();
  }

  protected function registerDefaultConversion()
  {
    $thumbSize = config('media.thumbsize', []);
    if ($thumbSize && count($thumbSize) == 2) {
      Conversion::register('thumb', function (Image $image) use ($thumbSize) {
        return $image->fit($thumbSize[0], $thumbSize[1]);
      });
    }
  }

  protected function registerEvents()
  {
    Event::listen(MediaUploadedEvent::class, function (MediaUploadedEvent $event) {
      PerformConversions::dispatch(
        $event->media,
        $event->selectedFolder,
        ['thumb']
      );
    });
    Event::listen(GetVideoInformationEvent::class, GetVideoInformationListener::class);
    Event::listen(GetVideoInformationEvent::class, HandleVideoUploaded::class);
    // Event::listen(GetVideoInformationEvent::class, function(GetVideoInformationEvent $event) {
    //   $media = $event->media;
    //   if ($media->type == 'video' && !$media->processed) {
    //     HandleVideoUploaded::dispatch($media);
    //   }
    // });
  }

  protected function loadRoutes()
  {
    Route::middleware(['web', 'admin.auth'])
      ->prefix('admin')
      ->group(__DIR__ . '/../routes/admin.php');

    Route::middleware(['web'])
        ->group(__DIR__.'/../routes/web.php');
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
    
    Blade::include("media::admin.modals.tag-modal", 'modalMedia');
    Blade::include("media::admin.forms.file-manager", 'fileManager');
    Blade::include("media::admin.forms.single-file", 'singleFile');
    Blade::include("media::admin.forms.single-image", 'singleImage');
    Blade::include("media::admin.forms.media", 'media');
    Blade::include("media::admin.forms.gallery", 'gallery');
  }

  public function registerCommands()
  {
    if ($this->app->runningInConsole()) {
      $this->commands($this->commandsCLI);
    }
  }
}
