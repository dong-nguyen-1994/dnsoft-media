<?php

namespace DnSoft\Media\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use DnSoft\Media\Jobs\PerformConversions;
use DnSoft\Media\MediaGroup;
use DnSoft\Media\Models\Media;
use DnSoft\Media\Resources\MediaResource;

/**
 * Trait HasMediaTrait
 *
 * @package DnSoft\Media\Traits
 *
 * @property boolean $forceDeleteMedia
 */
trait HasMediaTraitV3
{
  /** @var MediaGroup[] */
  protected $mediaGroups = [];

  protected $mediaAttributes = [];

  protected static function bootHasMediaTraitV3()
  {
    static::deleting(function (self $model) {
      if ($model->forceDeleteMedia()) {
        foreach ($model->media as $media) {
          $media->delete();
        }
      } else {
        $model->media()->detach();
      }
    });

    static::saved(function (self $model) {
      foreach ($model->mediaAttributes as $key => $value) {
        $model->syncMedia($value, $key);
      }
    });
  }

  /**
   * Get the "media" relationship.
   * @return MorphToMany
   */
  public function media()
  {
    return $this
      ->morphToMany(config('media.model'), 'mediable')
      ->withPivot('group', 'id')
      ->orderBy('pivot_id')
      ->with('mediaTags');
  }

  /**
   * Determine if there is any media in the specified group.
   * @param string $group
   * @return mixed
   */
  public function hasMedia(string $group = 'default')
  {
    return $this->getMedia($group)->isNotEmpty();
  }

  /**
   * Get all the media in the specified group.
   * @param string $group
   * @return mixed
   */
  public function getMedia(string $group = 'default')
  {
    return $this->media->where('pivot.group', $group);
  }

  /**
   * Get the first media item in the specified group.
   * @param string $group
   * @return mixed
   */
  public function getFirstMedia(string $group = 'default')
  {
    return $this->getMedia($group)->first();
  }

  /**
   * Get the url of the first media item in the specified group.
   * @param string $group
   * @param string $conversion
   * @return string
   */
  public function getFirstMediaUrl(string $group = 'default', string $conversion = '')
  {
    if (!$media = $this->getFirstMedia($group)) {
      return '';
    }

    return $media->getUrl($media->folder()->first(), $conversion);
  }

  /**
   * Attach media to the specified group.
   * @param mixed  $media
   * @param string $group
   * @param array  $conversions
   * @return void
   */
  public function attachMedia($media, string $group = 'default', array $conversions = [])
  {
    $this->registerMediaGroups();

    $ids = $this->parseMediaIds($media);

    $mediaGroup = $this->getMediaGroup($group);

    if ($mediaGroup && $mediaGroup->hasConversions()) {
      $conversions = array_merge(
        $conversions,
        $mediaGroup->getConversions()
      );
    }

    if (!empty($conversions)) {
      $model = config('media.model');

      /** @var Media $media */
      $media = $model::findMany($ids);

      $media->each(function ($media) use ($conversions) {
        PerformConversions::dispatch(
          $media,
          $conversions
        );
      });
    }

    $this->media()->attach($ids, [
      'group' => $group,
    ]);
  }

  /**
   * Sync media to the specified group.
   *
   * @param $media
   * @param  string  $group
   * @param  array  $conversions
   */
  public function syncMedia($media, string $group = 'default', array $conversions = [])
  {
    $this->clearMediaGroup($group);
    $this->attachMedia($media, $group, $conversions);
  }

  /**
   * Register all the model's media groups.
   * @return void
   */
  public function registerMediaGroups()
  {
    //
  }

  /**
   * Get the media group with the specified name.
   * @param string $name
   * @return MediaGroup|null
   */
  public function getMediaGroup(string $name)
  {
    return $this->mediaGroups[$name] ?? null;
  }

  /**
   * Detach the specified media.
   * @param mixed $media
   * @return void
   */
  public function detachMedia($media = null)
  {
    $this->media()->detach($media);
  }

  /**
   * Detach all the media in the specified group.
   * @param string $group
   * @return void
   */
  public function clearMediaGroup(string $group = 'default')
  {
    $this->media()->wherePivot('group', $group)->detach();
  }

  /**
   * Parse the media id's from the mixed input.
   * @param mixed $media
   * @return array
   */
  protected function parseMediaIds($media)
  {
    if (is_string($media)) {
      return $this->convertMediaString($media);
    }
    if ($media instanceof Collection) {
      return $media->modelKeys();
    }

    if ($media instanceof Media) {
      return [$media->getKey()];
    }

    return (array)$media;
  }

  /**
   * Register a new media group.
   * @param string $name
   * @return MediaGroup
   */
  protected function addMediaGroup(string $name)
  {
    $group = new MediaGroup();

    $this->mediaGroups[$name] = $group;

    return $group;
  }

  protected function forceDeleteMedia()
  {
    return property_exists($this, 'forceDeleteMedia') ? $this->forceDeleteMedia : false;
  }

  /**
   * Convert string media to array
   * @param string
   * @return array
   */
  protected function convertMediaString($media): array
  {
    return json_decode($media, true);
  }

  /**
   * Get media conversion
   * @return string
   */
  abstract function getMediaConversion();

  /**
   * Get gallery data
   */
  public function getGalleryData($conversion = null)
  {
    $gallery = $this->getMedia($conversion ?? $this->getMediaConversion());
    $arrImage = []; $ids = [];
    foreach ($gallery as $media) {
      $arrImage[] = new MediaResource($media);
      $ids[] = $media->id;
    }
    return [
      'medias' => $arrImage,
      'ids' => $ids
    ];
  }

  /**
   * Get single file image
   */
  public function getImageData($conversion = null)
  {
    $file = $this->getFirstMedia($conversion ?? $this->getMediaConversion());
    if ($file) {
      return new MediaResource($file);
    }
    return null;
  }
}
