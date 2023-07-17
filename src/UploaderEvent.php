<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\Diactoros\ServerRequest;
use Laminas\EventManager\Event;
use Psr\Http\Message\ServerRequestInterface;

final class UploaderEvent extends Event
{
    public const EVENT_UPLOAD = 'handleUpload';
    public const EVENT_DELETE = 'handleDelete';

    protected array $files;
    protected ServerRequestInterface|ServerRequest $request;
    protected ?string $fileName;
    protected ?string $fileId;

    public function setRequest(ServerRequest $request): void
    {
        $this->request = $request;
    }

    public function getRequest(): ServerRequestInterface|ServerRequest
    {
        return $this->request;
    }

    public function setFiles($files)
    {
        $this->files = $files;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getFileName(): string|null
    {
        return $this->fileName;
    }

    public function setFileId(string $fileId): void
    {
        $this->fileId = $fileId;
    }

    public function getFileId(): string|null
    {
        return $this->fileId;
    }
}
