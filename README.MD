- use `` @modalMedia ``
- call service ``Dnsoft\Media\Services\StorageTagService;``
- EX:
``
$res = $this->projectRepository->updateById($request->all(), $id);
 $this->mediaService->storeTagMedia($request->all(), $id, $res);
``
