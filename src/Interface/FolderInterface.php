<?php

namespace DnSoft\Media\Interface;

interface FolderInterface
{
  public function handleGetList(int $folderId, string $breadcrumbs, $isFromBreadcumb): array;  
}
