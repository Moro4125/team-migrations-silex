<?php
/**
 * Class PdoPostgreSQLHandlerProvider
 */
namespace Moro\Migration\Provider\Handler;
use Pimple\Container;
use Silex\Application;
use Moro\Migration\Handler\PdoPostgreSQLHandler;

/**
 * Class PdoPostgreSQLHandlerProvider
 *
 *     $app->register(new TeamMigrationsServiceProvider(), [
 *       'team-migrations.options' => [
 *         'service.pdo' => 'db.connection',
 *       ],
 *       'team-migrations.providers' => [
 *         new PdoPostgreSQLHandlerProvider(),
 *       ],
 *     ]);
 *
 * @package Moro\Migration\Provider\Handler
 */
class PdoPostgreSQLHandlerProvider extends AbstractHandlerProvider
{
	const OPTION_CLASS_PDO_POSTGRESQL = 'class.pdo.postgresql';
	const OPTION_SERVICE_PDO          = 'service.pdo';

	/**
	 * @var array
	 */
	protected $_defaultOptions = [
		self::OPTION_CLASS_PDO_POSTGRESQL => PdoPostgreSQLHandler::class,
		self::OPTION_SERVICE_PDO          => 'db',
	];

	/**
	 * @param Application $app
	 * @param Container $options
	 * @param null|string $name
	 * @return \Moro\Migration\Handler\PdoPostgreSQLHandler
	 */
	protected function _register(Application $app, Container $options, $name = null)
	{
		$service = is_string($options[self::OPTION_SERVICE_PDO])
			? $app[$options[self::OPTION_SERVICE_PDO]]
			: $options[self::OPTION_SERVICE_PDO];

		/** @var \Moro\Migration\Handler\PdoPostgreSQLHandler $pdoPostgreSQLHandler */
		$classPdoPostgreSQL = $options[self::OPTION_CLASS_PDO_POSTGRESQL];
		$pdoPostgreSQLHandler = new $classPdoPostgreSQL($name);
		$pdoPostgreSQLHandler->setConnection($service);

		return $pdoPostgreSQLHandler;
	}
}