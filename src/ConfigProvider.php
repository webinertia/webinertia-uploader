<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

final class ConfigProvider
{
    public function getDependencyConfig(): array
    {
        return [
            UploaderListener::class => UploaderListenerFactory::class,
        ];
    }

    public function getListenerConfig(): array
    {
        return [
            UploaderListener::class,
        ];
    }

    public function getUploaderConfig(): array
    {
        return [

        ];
    }
}
