<?php

namespace DnSoft\Media\Repositories;

use DnSoft\Core\Repositories\BaseRepository;

class MediableRepository extends BaseRepository implements MediableRepositoryInterace
{
    public function paginate($itemOnPage)
    {
        return $this->model->orderBy('created_at', 'desc')->paginate($itemOnPage);
    }

    public function getByCondition($array)
    {
        return $this->model->where($array);
    }
}
