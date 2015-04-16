<?php
/**
 * Class FilesStorageHandlerProvider
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
 *         'path.storage' => '../storage',
 *       ],
 *       'team-migrations.providers' => [
 *         new FilesStorageHandlerProvider(),
 *       ],
 *     ]);
 *
 * @package Moro\Migration\Provider\Handler
 */
class FilesStorageHandlerProvider extends AbstractHandlerProvider
{
	const OPTION_CLASS_FILES_STORAGE = 'class.files.storage';
	const OPTION_PATH_STORAGE        = 'path.storage';

	/**
	 * @var array
	 */
	protected $_defaultOptions = [
		self::OPTION_CLASS_FILES_STORAGE => FilesStorageHandler::class,
		self::OPTION_PATH_STORAGE        => 'storage',
	];

	/**
	 * @param Application $app
	 * @param Pimple $options
	 * @param null|string $name
	 * @return \Moro\Migration\Handler\FilesStorageHandler
	 */
	protected function _register(Application $app, Pimple $options, $name = null)
	{
		if (($storagePath = $options[self::OPTION_PATH_STORAGE]) && $storagePath[0] != '/' && $storagePath[1] != ':')
		{
			$storagePath = $options[TeamMigrationsServiceProvider::OPTION_PATH_PROJECT].DIRECTORY_SEPARATOR.$storagePath;
		}

		/** @var \Moro\Migration\Handler\FilesStorageHandler $filesStorageHandler */
		$classFilesStorage = $options[self::OPTION_CLASS_FILES_STORAGE];
		$filesStorageHandler = new $classFilesStorage($name);
		$filesStorageHandler->setStoragePath($storagePath);

		return $filesStorageHandler;
	}
}