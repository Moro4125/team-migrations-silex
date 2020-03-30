<?php
/**
 * Class AbstractHandlerProvider
 */
namespace Moro\Migration\Provider\Handler;

use Moro\Migration\MigrationManager;
use Moro\Migration\Provider\AbstractServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use RuntimeException;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractHandlerProvider
 * @package Moro\Migration\Provider\Handler
 */
abstract class AbstractHandlerProvider extends AbstractServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    const ERROR_SERVICE_ALREADY_EXISTS = 'Error, service "%1$s" already exists!';

    /**
     * @var array
     */
    protected $_defaultOptions = [];

    /**
     * @var string
     */
    protected $_name;

    /**
     * @param null|string $name
     */
    public function __construct($name = null)
    {
        is_string($name) && $this->_name = $name;
    }

    /**
     * @param Container|Application $app
     */
    final public function register(Container $app)
    {
        $options = isset($app[self::TEAM_MIGRATIONS_DEFAULT]) ? $app[self::TEAM_MIGRATIONS_DEFAULT] : [];
        $app[self::TEAM_MIGRATIONS_DEFAULT] = array_merge($options, $this->_getDefaultOptions($app));

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $app['dispatcher'];
        $dispatcher->addListener(MigrationManager::EVENT_INIT_SERVICE, function ($event) use ($app, $dispatcher) {
            /** @var Container $config */
            $config = clone $app[self::TEAM_MIGRATIONS_CONFIG];

            if ($this->_name) {
                $optionsKey = str_replace('.', '.' . $this->_name . '.', self::TEAM_MIGRATIONS_OPTIONS);

                foreach (isset($app[$optionsKey]) ? $app[$optionsKey] : [] as $key => $value) {
                    $config[$key] = $value;
                }

                unset($app[$optionsKey]);
            }

            $handler = $this->_register($app, $config,
                $this->_name ? self::TEAM_MIGRATIONS . '.' . $this->_name : null);

            if (isset($app[$handler->getServiceName()])) {
                throw new RuntimeException(sprintf(self::ERROR_SERVICE_ALREADY_EXISTS, $handler->getServiceName()));
            }

            $dispatcher->addSubscriber($handler);

            $app[$handler->getServiceName()] = $handler;
            $handler->update($event);
        });
    }

    /**
     * @param Application $app
     */
    final public function boot(Application $app)
    {
    }

    /**
     * @param Container $app
     * @return array
     */
    protected function _getDefaultOptions(
        /** @noinspection PhpUnusedParameterInspection */
        Container $app
    ) {
        return $this->_defaultOptions;
    }

    /**
     * @param Application $app
     * @param Container $options
     * @param null|string $name
     * @return \Moro\Migration\Handler\AbstractHandler
     */
    abstract protected function _register(Application $app, Container $options, $name = null);
}