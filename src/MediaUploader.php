<?php

namespace DnSoft\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use DnSoft\Media\Events\MediaUploadedEvent;
use DnSoft\Media\Exceptions\CantUploadException;
use DnSoft\Media\Models\Folder;
use DnSoft\Media\Models\Media;

class MediaUploader
{
  /** @var UploadedFile */
  protected $file;

  /** @var string */
  protected $name;

  /** @var string */
  protected $fileName;

  /** @var array */
  protected $attributes = [];

  /**
   * Set the file to be uploaded.
   * @param UploadedFile $file
   * @return MediaUploader
   */
  public function setFile(UploadedFile $file)
  {
    $this->file = $file;

    $fileName = $file->getClientOriginalName();
    $name = pathinfo($fileName, PATHINFO_FILENAME);

    $this->setName($name);
    $this->setFileName($fileName);

    return $this;
  }

  /**
   * Set the name of the media item.
   * @param string $name
   * @return MediaUploader
   */
  public function setName(string $name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Set the name of the file.
   * @param string $fileName
   * @return MediaUploader
   */
  public function setFileName(string $fileName)
  {
    $this->fileName = $this->sanitiseFileName($fileName);

    return $this;
  }

  /**
   * Sanitise the file name.
   * @param string $fileName
   * @return string
   */
  protected function sanitiseFileName(string $fileName)
  {
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    $name = pathinfo($fileName, PATHINFO_FILENAME);

    return Str::lower(Str::slug($name) . '.' . $ext);
  }

  /**
   * Set any custom attributes to be saved to the media item.
   * @param array $attributes
   * @return MediaUploader
   */
  public function withAttributes(array $attributes)
  {
    $this->attributes = $attributes;

    return $this;
  }

  /**
   * @param array $properties
   * @return MediaUploader
   */
  public function withProperties(array $properties)
  {
    return $this->withAttributes($properties);
  }

  /**
   * Upload the file to the specified disk.
   * @return Media
   */
  public function upload(Folder $selectedFolder)
  {
    $model = config('media.model');
    $isExisted = $model::where(['file_name' => $this->fileName, 'folder_id' => $selectedFolder ? $selectedFolder->id : null])->first();
    if ($isExisted) {
      return [$isExisted];
    }

    /** @var Media $media */
    $media = new $model();

    $media->name = $this->name;
    $media->file_name = $this->fileName;
    $media->alt = $this->name;
    $media->disk = config('media.disk');
    $media->mime_type = $this->file->getMimeType();
    $media->size = $this->file->getSize();
    if ($selectedFolder) {
      $media->folder_id = $selectedFolder->id;
    }
    if ($auth = $this->getAuthor()) {
      $media->author()->associate($auth);
    }
    $media->forceFill($this->attributes);
    $media->save();
    $media->filesystem()->putFileAs(
      $media->getDirectory($selectedFolder->name),
      $this->file,
      $this->fileName,
      [
        'visibility' => 'public',
      ]
    );
    event(new MediaUploadedEvent($media, $selectedFolder));

    return $media->fresh();
  }

  protected function getAuthor()
  {
    $guard = config('media.guard');
    return \Auth::guard($guard)->user();
  }
}
