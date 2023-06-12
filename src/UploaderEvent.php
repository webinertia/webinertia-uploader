<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\EventManager\Event;

class UploaderEvent extends Event
{
    public const EVENT_UPLOAD = 'upload';
    public const EVENT_DELETE = 'delete';
}
