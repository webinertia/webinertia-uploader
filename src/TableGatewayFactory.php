<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\Db;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class TableGatewayFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): TableGateway
    {
        if (! $container->has('config') || ! isset($container->get('config')['upload_manager'])) {
            throw new ServiceNotFoundException('config service could not be located.');
        }
        $config   = $container->get('config');
        $resultSetPrototype = new Db\ResultSet\HydratingResultSet(new ArraySerializableHydrator());
        $resultSetPrototype->setObjectPrototype(new UploadManager($config, null, null));
        return new $requestedName(
            $config[ConfigProvider::CONFIG_KEY]['db_config']['table_name'] ?? ConfigProvider::TABLE_NAME,
            $container->get('EventManager'),
            $resultSetPrototype,
            $config[ConfigProvider::CONFIG_KEY]['db_config']['enable_events'] ?? ConfigProvider::ENABLE_GATEWAY_EVENTS,
            $config[ConfigProvider::CONFIG_KEY]['db_config']['gateway_listener'] ?? null,
            $container->get(AdapterInterface::class)
        );
    }
}
