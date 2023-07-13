<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

final class ConfigProvider
{
    public const CONFIG_KEY            = 'upload_manager';
    public const TABLE_NAME            = 'upload';
    public const ENABLE_GATEWAY_EVENTS = false;

    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                UploaderListener::class => UploaderListenerFactory::class,
                UploadManager::class    => UploadManagerFactory::class,
                TableGateway::class     => TableGatewayFactory::class,
            ],
        ];
    }

    public function getListenerConfig(): array
    {
        return [
            UploaderListener::class,
        ];
    }

    public function getComponentConfig(): array
    {
        return [
            'handler_config' => [
                'overwrite'            => true,
                'randomize'            => true,
                'use_upload_extension' => true,
            ],
            'db_config' => [
                'table_name'       => null, // null or db table name, defaults to upload, see above
                'enable_events'    => null, // null or true
                'gateway_listener' => null, // null or class-string SomeListener::class
            ],
        ];
    }
}
