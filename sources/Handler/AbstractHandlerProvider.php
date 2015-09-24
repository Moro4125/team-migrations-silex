<?php
/**
 * Class AbstractHandlerProvider
 */
namespace Moro\Migration\Provider\Handler;
use \Pimple;
use \Silex\Application;
use \Moro\Migration\MigrationManager;
use \Moro\Migration\Provider\AbstractServiceProvider;
use \RuntimeException;

/**
 * Class AbstractHandlerProvider
 * @package Moro\Migration\Provider\Handler
 */
abstract class AbstractHandlerProvider extends AbstractServiceProvider
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
	 * @param Application $app
	 */
	final public function register(Application $app)
	{
		$options = isset($app[self::TEAM_MIGRATIONS_DEFAULT]) ? $app[self::TEAM_MIGRATIONS_DEFAULT] : [];
		$app[self::TEAM_MIGRATIONS_DEFAULT] = array_merge($options, $this->_getDefaultOptions($app));

		/** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */
		$dispatcher = $app['dispatcher'];
		$dispatcher->addListener(MigrationManager::EVENT_INIT_SERVICE, function($event) use ($app) {
			/** @var Pimple $config */
			$config = clone $app[self::TEAM_MIGRATIONS_CONFIG];

			if ($this->_name)
			{
				$optionsKey = str_replace('.', '.'.$this->_name.'.', self::TEAM_MIGRATIONS_OPTIONS);

				foreach (isset($app[$optionsKey]) ? $app[$optionsKey] : [] as $key => $value)
				{
					$config[$key] = $value;
				}

				unset($app[$optionsKey]);
			}

			$handler = $this->_register($app, $config, $this->_name ? self::TEAM_MIGRATIONS.'.'.$this->_name : null);

			if (isset($app[$handler->getServiceName()]))
			{
				throw new RuntimeException(sprintf(self::ERROR_SERVICE_ALREADY_EXISTS, $handler->getServiceName()));
			}

			/** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */
			$dispatcher = $app['dispatcher'];
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
	 * @param null|string $name
	 * @return \Moro\Migration\Handler\AbstractHandler
	 */
	abstract protected function _register(Application $app, Pimple $options, $name = null);
}