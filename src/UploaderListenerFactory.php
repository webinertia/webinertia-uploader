<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Service\UserServiceInterface; // todo abstract this out

class UploaderListenerFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UploaderListener
    {
        return new $requestedName($container->get(UserServiceInterface::class));
    }
}
