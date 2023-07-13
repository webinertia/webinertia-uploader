<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;

final class UploaderListenerFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UploaderListener
    {
        /** @var \Laminas\Diactoros\ServerRequestFactory $factory */
        $factory = $container->get(ServerRequestFactoryInterface::class);
        return new $requestedName(
            $factory::fromGlobals(),
            $container->get(UploadManager::class),
        );
    }
}
