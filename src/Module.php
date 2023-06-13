<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

final class Module
{
    public function getConfig(): array
    {
        $configProvider = new ConfigProvider();
        return [
            'service_manager' => $configProvider->getDependencyConfig(),
            'listeners'       => $configProvider->getListenerConfig(),
            'uploader'        => $configProvider->getUploaderConfig(),
        ];
    }
}
