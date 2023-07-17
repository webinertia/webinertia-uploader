<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\Filter\File\RenameUpload;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Webinertia\Filter\Uuid;

final class UploadManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UploadManager
    {
        $config = $container->get('config');
        if (! isset($config[ConfigProvider::CONFIG_KEY]['handler_config'])) {
            return new $requestedName();
        }
        $filter = new RenameUpload(
            array_merge(
                $config[ConfigProvider::CONFIG_KEY]['handler_config'],
                [
                    'stream_factory'      => $container->get(StreamFactoryInterface::class),
                    'upload_file_factory' => $container->get(UploadedFileFactoryInterface::class)
                ]
            )
        );
        $uuid = new Uuid();
        return new $requestedName(
            $config,
            $container->get(TableGateway::class),
            $filter,
            $uuid,
            []
        );
    }
}
