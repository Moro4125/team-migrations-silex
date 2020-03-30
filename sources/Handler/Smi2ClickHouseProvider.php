<?php
/**
 * Class Smi2ClickHouseProvider
 */

namespace Moro\Migration\Provider\Handler;

use Moro\Migration\Handler\Smi2ClickHouseHandler;
use Pimple\Container;
use Silex\Application;

/**
 * Class Smi2ClickHouseProvider
 * @package Moro\Migration\Provider\Handler
 */
class Smi2ClickHouseProvider extends AbstractHandlerProvider
{
    const OPTION_CLASS_SMI2_CH  = 'class.smi2.clickhouse';
    const OPTION_SERVICE_CLIENT = 'service.smi2.clickhouse';

    /**
     * @var array
     */
    protected $_defaultOptions = [
        self::OPTION_CLASS_SMI2_CH  => Smi2ClickHouseHandler::class,
        self::OPTION_SERVICE_CLIENT => 'clickhouse.client',
    ];

    /**
     * @param Application $app
     * @param Container $options
     * @param null|string $name
     * @return \Moro\Migration\Handler\Smi2ClickHouseHandler
     */
    protected function _register(Application $app, Container $options, $name = null)
    {
        $service = is_string($options[self::OPTION_SERVICE_CLIENT]) ? $app[$options[self::OPTION_SERVICE_CLIENT]] : $options[self::OPTION_SERVICE_CLIENT];

        /** @var \Moro\Migration\Handler\Smi2ClickHouseHandler $pdoSmi2ClickHouseHandler */
        $classSmi2ClickHouse = $options[self::OPTION_CLASS_SMI2_CH];
        $pdoSmi2ClickHouseHandler = new $classSmi2ClickHouse($service, $name);

        return $pdoSmi2ClickHouseHandler;
    }
}