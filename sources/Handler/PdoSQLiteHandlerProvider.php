<?php
/**
 * Class PdoSQLiteHandlerProvider
 */
namespace Moro\Migration\Provider\Handler;
use Pimple\Container;
use Silex\Application;
use Moro\Migration\Handler\PdoSQLiteHandler;

/**
 * Class PdoSQLiteHandlerProvider
 *
 *     $app->register(new TeamMigrationsServiceProvider(), [
 *       'team-migrations.options' => [
 *         'service.pdo' => 'db.connection',
 *       ],
 *       'team-migrations.providers' => [
 *         new PdoSQLiteHandlerProvider(),
 *       ],
 *     ]);
 *
 * @package Moro\Migration\Provider\Handler
 */
class PdoSQLiteHandlerProvider extends AbstractHandlerProvider
{
	const OPTION_CLASS_PDO_SQLITE = 'class.pdo.sqlite';
	const OPTION_SERVICE_PDO      = 'service.pdo';

	/**
	 * @var array
	 */
	protected $_defaultOptions = [
		self::OPTION_CLASS_PDO_SQLITE => PdoSQLiteHandler::class,
		self::OPTION_SERVICE_PDO      => 'db',
	];

	/**
	 * @param Application $app
	 * @param Container $options
	 * @param null|string $name
	 * @return \Moro\Migration\Handler\PdoSQLiteHandler
	 */
	protected function _register(Application $app, Container $options, $name = null)
	{
		$service = is_string($options[self::OPTION_SERVICE_PDO])
			? $app[$options[self::OPTION_SERVICE_PDO]]
			: $options[self::OPTION_SERVICE_PDO];

		/** @var \Moro\Migration\Handler\PdoSQLiteHandler $pdoSQLiteHandler */
		$classPdoSQLite = $options[self::OPTION_CLASS_PDO_SQLITE];
		$pdoSQLiteHandler = new $classPdoSQLite($name);
		$pdoSQLiteHandler->setConnection($service);

		return $pdoSQLiteHandler;
	}
}