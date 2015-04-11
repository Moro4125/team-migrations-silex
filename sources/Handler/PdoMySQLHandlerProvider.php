<?php
/**
 * Class PdoMySQLHandlerProvider
 */
namespace Moro\Migration\Provider\Handler;
use \Pimple;
use \Silex\Application;
use \Moro\Migration\Handler\FilesStorageHandler;
use \Moro\Migration\Provider\TeamMigrationsServiceProvider;

/**
 * Class FilesStorageHandlerProvider
 *
 *     $app->register(new TeamMigrationsServiceProvider(), [
 *       'team-migrations.options' => [
 *         'service.pdo' => 'db.connection',
 *       ],
 *       'team-migrations.providers' => [
 *         new PdoMySQLHandlerProvider(),
 *       ],
 *     ]);
 *
 * @package Moro\Migration\Provider\Handler
 */
class PdoMySQLHandlerProvider extends AbstractHandlerProvider
{
	const OPTION_CLASS_PDO_MYSQL = 'class.pdo.mysql';
	const OPTION_SERVICE_PDO     = 'service.pdo';

	/**
	 * @var array
	 */
	protected $_defaultOptions = [
		self::OPTION_CLASS_PDO_MYSQL => FilesStorageHandler::class,
		self::OPTION_SERVICE_PDO     => 'db',
	];

	/**
	 * @param Application $app
	 * @param Pimple $options
	 * @return \Moro\Migration\Handler\AbstractHandler
	 */
	protected function _register(Application $app, Pimple $options)
	{
		$service = is_string($options[self::OPTION_SERVICE_PDO])
			? $app[$options[self::OPTION_SERVICE_PDO]]
			: $options[self::OPTION_SERVICE_PDO];

		/** @var \Moro\Migration\Handler\PdoMySQLHandler $pdoMySQLHandler */
		$classPdoMySQL = $options[self::OPTION_CLASS_PDO_MYSQL];
		$pdoMySQLHandler = new $classPdoMySQL;
		$pdoMySQLHandler->setConnection($service);

		return $pdoMySQLHandler;
	}
}