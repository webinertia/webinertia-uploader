<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\AbstractListenerAggregate;

use function method_exists;

final class UploaderListener extends AbstractListenerAggregate
{
    /** @inheritDoc */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedManager     = $events->getSharedManager();
        $this->listeners[] = $sharedManager->attach(
            UploaderAwareInterface::class,
            UploaderEvent::EVENT_UPLOAD,
            [$this, 'upload'],
            $priority
        );
        $this->listeners[] = $sharedManager->attach(
            UploaderAwareInterface::class,
            UploaderEvent::EVENT_DELETE,
            [$this, 'delete'],
            $priority
        );
    }

    public function upload(EventInterface $event): mixed
    {
        $target = $event->getTarget();
        if (! method_exists($target, 'handleUpload')) {
            throw new Exception\UnknownHandlerException(
                'EventHandler handleUpload is not a method of target: ' . $target::class
            );
        }

        return $target->handleUpload($event->getParams()) ? true : false;
    }

    public function delete(EventInterface $event): mixed
    {
        $target = $event->getTarget();
        if (! method_exists($target, 'handleDelete')) {
            throw new Exception\UnknownHandlerException(
                'EventHandler handleDelete is not a method of target: ' . $target::class
            );
        }

        return $target->handleDelete($event->getParams()) ? true : false;
    }
}
