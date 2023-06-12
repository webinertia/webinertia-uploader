<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use App\Log\LogEvent;
use App\Log\LoggerAwareInterface;
use App\Log\LoggerAwareInterfaceTrait;
use App\Upload\UploadEvent;
use App\Upload\Exception\UnknownHandlerException;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\Router\Http\Method;
use User\Service\UserServiceInterface;
use User\Service\UserService;

use function method_exists;

class UploaderListener extends AbstractListenerAggregate implements LoggerAwareInterface
{
    use LoggerAwareInterfaceTrait; // move this to event handling

    /** @var UserService $userService */
    protected $userService;

    /** todo remove this dependency */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }
    /** @inheritDoc */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedManager     = $events->getSharedManager();
        $this->listeners[] = $sharedManager->attach(
            UploadAwareInterface::class,
            UploadEvent::EVENT_UPLOAD,
            [$this, 'upload'],
            $priority
        );
        $this->listeners[] = $sharedManager->attach(
            UploadAwareInterface::class,
            UploadEvent::EVENT_DELETE,
            [$this, 'delete'],
            $priority
        );
    }

    public function upload(EventInterface $event): mixed
    {
        $name   = $event->getName();
        $target = $event->getTarget();
        $params = $event->getParams();
        if (! method_exists($target, 'handleUpload')) {
            throw new UnknownHandlerException('EventHandler handleUpload is not a method of target: ' . $target::class);
        }
        // move logging to an event
        if ($target->handleUpload($params)) {
            $this->getEventManager()->trigger(
                LogEvent::INFO,
                'User: {firstName} {lastName} uploaded an image',
                $this->userService->getLogData()
            );
            return true;
        } else {
            $this->getEventManager()->trigger(
                LogEvent::INFO,
                'User: {firstName} {lastName}\'s upload failed!',
                $this->userService->getLogData()
            );
            return false;
        }
    }

    public function delete(EventInterface $event): mixed
    {
        $name   = $event->getName();
        $target = $event->getTarget();
        $params = $event->getParams();
        if (! method_exists($target, 'handleDelete')) {
            throw new UnknownHandlerException('EventHandler handleDelete is not a method of target: ' . $target::class);
        }
        if($target->handleDelete($event->getParams())) {
            $this->getEventManager()->trigger(
                LogEvent::INFO,
                'User: {firstName} {lastName} deleted an image',
                $this->userService->getLogData()
            );
            return true;
        } else {
            $this->getEventManager()->trigger(
                LogEvent::INFO,
                'User: {firstName} {lastName} encountered an image deletion failure',
                $this->userService->getLogData()
            );
        }
    }
}
