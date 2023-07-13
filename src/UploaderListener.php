<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ResponseCollection;
use Psr\Http\Message\ServerRequestInterface;

use function method_exists;

final class UploaderListener extends AbstractListenerAggregate
{
    public function __construct(
        protected ServerRequestInterface $request,
        protected UploadManager $manager
    ) {
    }
    /** @inheritDoc */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedManager     = $events->getSharedManager();
        $this->listeners[] = $sharedManager->attach(
            UploaderAwareInterface::class,
            UploaderEvent::EVENT_UPLOAD,
            [$this, 'handleUpload'],
            $priority
        );
        $this->listeners[] = $sharedManager->attach(
            UploaderAwareInterface::class,
            UploaderEvent::EVENT_DELETE,
            [$this, 'handleDelete'],
            $priority
        );
    }

    public function handleUpload(UploaderEvent $event)
    {
        $event->setRequest($this->request);
        return $this->manager->handleUpload($event);
    }

    public function handleDelete(UploaderEvent $event)
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
