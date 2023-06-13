<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class UploaderListenerFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UploaderListener
    {
        return new $requestedName();
    }
}
