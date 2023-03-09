<?php

namespace DnSoft\Media\Traits;

use DnSoft\Media\Models\MediaFileManager;
use Illuminate\Support\Facades\Auth;

trait HasMediaTraitFileManager
{
    protected $mediaAttributes = [];

    protected static function bootHasMediaTraitFileManager()
    {
        static::saved(function (self $model) {
            foreach ($model->mediaAttributes as $key => $value) {
                $model->attachMediaFileManager($value, $key);
            }
        });
    }
    /**
     * Attach media to the specified group.
     * @param mixed $media
     * @param string $group
     * @param array $conversions
     * @return void
     */
    public function attachMediaFileManager($filesName, $group = 'gallery')
    {
        MediaFileManager::where(['table_type' => get_class($this), 'table_id' => $this->id, 'group' => $group])->delete();
        if ($filesName) {
            $arrFileName = explode(',', $filesName);
            foreach ($arrFileName as $fileName) {
                if ($fileName) {
                    $media = MediaFileManager::create([
                        'file_name' => $fileName,
                        'group' => $group
                    ]);
                    $media->table()->associate($this);
                    $media->author()->associate(Auth::guard('admin')->user());
                    $media->save();
                }
            }
        }
    }

    public function getFirstMedia($model, $group = 'gallery', $convension = 'thumbs')
    {
        $media = MediaFileManager::select('file_name')
            ->whereGroup($group)
            ->whereTableType(get_class($this))
            ->whereTableId($this->id)
            ->first();

        if ($media) {
            return get_url_file_path(). $model. '/'. $convension . '/' .$media['file_name'];
        }

        return null;
    }

    public function getGallery($model, $group = 'gallery', $convension = 'thumbs')
    {
        $gallery = MediaFileManager::select('file_name')
            ->whereGroup($group)
            ->whereTableType(get_class($this))
            ->whereTableId($this->id)
            ->get()->toArray();
        $files = [];
        $storagePath = get_url_file_path();
        foreach ($gallery as $item) {
            $file = [
                'name' => $item['file_name'],
                'url' => $storagePath . $model. '/'. $convension . '/' .$item['file_name'],
                'key' => generate_random_string(20)
            ];
            array_push($files, $file);
        }
        return $files;
    }

    private function analyticUrl($url)
    {
        $urls = explode('/', $url);
        unset($urls[count($urls) - 1]);
        return implode('/', $urls);
    }

    public function attachSeoMeta($value, $group)
    {
        if ($value) {
            MediaFileManager::where(['table_type' => get_class($this), 'table_id' => $this->id, 'group' => $group])->delete();
            $fileName = explode('/', $value);
            $media = MediaFileManager::create([
                'file_name' => $fileName[count($fileName) - 1],
                'url' => $value,
                'group' => $group,
            ]);
            $media->table()->associate($this);
            $media->author()->associate(Auth::guard('admin')->user());
            $media->save();
        }
    }

    public function getSeoMedia($group, $convension = 'thumbs')
    {
        $media = MediaFileManager::select('url', 'file_name')->where([
            'table_type' => get_class($this),
            'table_id' => $this->id,
            'group' => $group
        ])->first();
        if ($media) {
            if ($convension == 'thumbs') {
                $url = $this->analyticUrl($media->url);
                $url = $url . '/' . $convension . '/' . $media->file_name;
            } else {
                $url = $media->url;
            }
            return $url;
        }
        return null;
    }
}
