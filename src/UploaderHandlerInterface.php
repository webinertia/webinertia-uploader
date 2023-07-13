<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

interface UploaderHandlerInterface
{
    public function handleUpload(UploaderEvent $event);
    public function handleDelete(UploaderEvent $event);
}
