<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerInterface;

interface UploaderAwareInterface extends EventManagerAwareInterface
{
    public function getEventManager(): EventManagerInterface;
}
