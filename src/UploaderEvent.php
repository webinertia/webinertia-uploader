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
}
