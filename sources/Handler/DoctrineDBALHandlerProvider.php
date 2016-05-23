<?php
/**
 * Class DoctrineDBALHandlerProvider
 */
namespace Moro\Migration\Provider\Handler;
use Pimple\Container;
use Silex\Application;
use Moro\Migration\Handler\DoctrineDBALHandler;

/**
 * Class DoctrineDBALHandlerProvider
 *
 *     $app->register(new TeamMigrationsServiceProvider(), [
 *       'team-migrations.options' => [
 *         'service.doctrine.connection' => 'db',
 *       ],
 *       'team-migrations.providers' => [
 *         new DoctrineDBALHandlerProvider(),
 *       ],
 *     ]);
 *
 * @package Moro\Migration\Provider\Handler
 */
class DoctrineDBALHandlerProvider extends AbstractHandlerProvider
{
	const OPTION_CLASS_DOCTRINE_DBAL         = 'class.doctrine.dbal';
	const OPTION_SERVICE_DOCTRINE_CONNECTION = 'service.doctrine.connection';

	/**
	 * @var array
	 */
	protected $_defaultOptions = [
		self::OPTION_CLASS_DOCTRINE_DBAL         => DoctrineDBALHandler::class,
		self::OPTION_SERVICE_DOCTRINE_CONNECTION => 'db',
	];

	/**
	 * @param Application $app
	 * @param Container $options
	 * @param null|string $name
	 * @return \Moro\Migration\Handler\DoctrineDBALHandler
	 */
	protected function _register(Application $app, Container $options, $name = null)
	{
		$service = is_string($options[self::OPTION_SERVICE_DOCTRINE_CONNECTION])
			? $app[$options[self::OPTION_SERVICE_DOCTRINE_CONNECTION]]
			: $options[self::OPTION_SERVICE_DOCTRINE_CONNECTION];

		/** @var \Moro\Migration\Handler\DoctrineDBALHandler $doctrineDBALHandler */
		$classDoctrineDBALHandler = $options[self::OPTION_CLASS_DOCTRINE_DBAL];
		$doctrineDBALHandler = new $classDoctrineDBALHandler($name);
		$doctrineDBALHandler->setConnection($service);

		return $doctrineDBALHandler;
	}
}