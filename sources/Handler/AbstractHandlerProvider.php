<?php
/**
 * Class AbstractHandlerProvider
 */
namespace Moro\Migration\Provider\Handler;
use \Pimple;
use \Silex\Application;
use \Moro\Migration\MigrationManager;
use \Moro\Migration\Provider\AbstractServiceProvider;

/**
 * Class AbstractHandlerProvider
 * @package Moro\Migration\Provider\Handler
 */
abstract class AbstractHandlerProvider extends AbstractServiceProvider
{
	/**
	 * @var array
	 */
	protected $_defaultOptions = [];

	/**
	 * @param Application $app
	 */
	final public function register(Application $app)
	{
		$options = isset($app[self::TEAM_MIGRATIONS_DEFAULT]) ? $app[self::TEAM_MIGRATIONS_DEFAULT] : [];
		$app[self::TEAM_MIGRATIONS_DEFAULT] = array_merge($options, $this->_getDefaultOptions($app, $options));

		/** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */
		$dispatcher = $app['dispatcher'];
		$dispatcher->addListener(MigrationManager::EVENT_INIT_SERVICE, function($event) use ($app) {
			/** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */
			$dispatcher = $app['dispatcher'];
			$handler = $this->_register($app, $app[self::TEAM_MIGRATIONS_CONFIG]);

			$dispatcher->addSubscriber($handler);
			$app[$handler->getServiceName()] = $handler;
			$handler->update($event);
		});
	}

	/**
	 * @param Application $app
	 * @return array
	 */
	protected function _getDefaultOptions(/** @noinspection PhpUnusedParameterInspection */ Application $app)
	{
		return $this->_defaultOptions;
	}

	/**
	 * @param Application $app
	 * @param Pimple $options
	 * @return \Moro\Migration\Handler\AbstractHandler
	 */
	abstract protected function _register(Application $app, Pimple $options);
}