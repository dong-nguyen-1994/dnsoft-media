<?php

namespace Dnsoft\Media\Traits;

use Dnsoft\Media\Models\MediaFileManager;
use Illuminate\Support\Facades\Auth;

trait HasImageTraitManager
{
    protected $mediaAttributes = [];

    protected static function bootHasImageTraitManager()
    {
        static::saved(function (self $model) {
            foreach ($model->mediaAttributes as $key => $value) {
                $model->attachImageManager($value, $key);
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
    public function attachImageManager($filesName, $group = 'gallery')
    {
        MediaFileManager::where(['table_type' => get_class($this), 'table_id' => $this->id, 'group' => $group])->delete();
        if ($filesName) {
            $arrFileName = array_values(array_filter(explode(',', $filesName)));
            foreach ($arrFileName as $fileName) {
                $arrDataImage = explode('/', $fileName);
                $nameOfFile = $arrDataImage[count($arrDataImage) - 1];
                $remainArrItems = array_filter($arrDataImage, function($item, $index) use ($arrDataImage) {
                    return $index < count($arrDataImage) - 1;
                }, ARRAY_FILTER_USE_BOTH);
                $urlRemain = implode('/', $remainArrItems);

                if ($fileName) {
                    $media = MediaFileManager::create([
                        'file_name' => $nameOfFile,
                        'group' => $group,
                        'origin_path' => $fileName,
                        'thumbnail_path' => $urlRemain.'/thumbs/'.$nameOfFile
                    ]);
                    $media->table()->associate($this);
                    $media->author()->associate(Auth::guard('admin')->user());
                    $media->save();
                }
            }
        }
    }

    public function getFirstMedia($group = 'gallery', $convension = 'thumbs')
    {
        if ($convension === 'thumbs') {
            $query = MediaFileManager::select('thumbnail_path');
        } else {
            $query = MediaFileManager::select('origin_path');
        }
        $media = $query->whereGroup($group)
            ->whereTableType(get_class($this))
            ->whereTableId($this->id)
            ->first();

        if ($media) {
            $uri = $media->thumbnail_path ? $media->thumbnail_path : $media->origin_path;
            return get_url_file_path(). $uri;
        }

        return null;
    }

    public function getGallery($group = 'gallery', $convension = 'thumbs')
    {
        if ($convension === 'thumbs') {
            $query = MediaFileManager::select('file_name', 'thumbnail_path');
        } else {
            $query = MediaFileManager::select('file_name', 'origin_path');
        }
        $gallery = $query
            ->whereGroup($group)
            ->whereTableType(get_class($this))
            ->whereTableId($this->id)
            ->get();
        $files = [];
        $storagePath = get_url_file_path();
        foreach ($gallery as $item) {
            $uri = $item->thumbnail_path ? $item->thumbnail_path : $item->origin_path;
            $file = [
                'name' => $item->file_name,
                'url' => $storagePath . $uri,
                'key' => generate_random_string(20)
            ];
            array_push($files, $file);
        }
        return $files;
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

    /**
     * Analytic url
     */
    private function analyticUrl($url)
    {
        $urls = explode('/', $url);
        unset($urls[count($urls) - 1]);
        return implode('/', $urls);
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
