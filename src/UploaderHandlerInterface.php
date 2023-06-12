<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

interface UploaderHandlerInterface
{
    public const FIELDSET = 'file-data';
    public function handleUpload(array $fileData);
    public function handleDelete(array $fileData);
}
