<?php
/**
 * Class TeamMigrationsServiceProvider
 */
namespace Moro\Migration\Provider;
use \Pimple;
use \Silex\Application;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Knp\Console\ConsoleEvents;
use \Knp\Console\ConsoleEvent;
use \Moro\Migration\MigrationManager;
use \Moro\Migration\Command\MigrationsCreate;
use \Moro\Migration\Command\MigrationsMigrate;
use \Moro\Migration\Command\MigrationsStatus;

/**
 * Class TeamMigrationsServiceProvider
 *
 *     $app->register(new TeamMigrationsServiceProvider(), [
 *       'team-migrations.options' => [
 *         'environment' => 'development',
 *         'path.project' => dirname(__DIR__),
 *       ],
 *       'team-migrations.providers' => [
 *       ],
 *     ]);
 *
 * @package Moro\Migration\Provider
 */
class TeamMigrationsServiceProvider extends AbstractServiceProvider
{
	const VERSION = '1.4.0-dev';

	const OPTION_ENVIRONMENT    = 'environment';
	const OPTION_CLASS_MANAGER  = 'class.manager';
	const OPTION_VALIDATION_KEY = 'validation.key';
	const OPTION_PATH_PROJECT   = 'path.project';

	/**
	 * @param Application $app
	 */
	public function register(Application $app)
	{
		$app[self::TEAM_MIGRATIONS_OPTIONS] = [];

		$app[self::TEAM_MIGRATIONS_DEFAULT] = [
			self::OPTION_ENVIRONMENT    => getenv('ENVIRONMENT') ?: 'production',
			self::OPTION_CLASS_MANAGER  => MigrationManager::class,
			self::OPTION_VALIDATION_KEY => '',
		];

		$app[self::TEAM_MIGRATIONS_CONFIG] = $app->share(function() use ($app) {
			$options = array_merge($app[self::TEAM_MIGRATIONS_DEFAULT], $app[self::TEAM_MIGRATIONS_OPTIONS]);

			unset($app[self::TEAM_MIGRATIONS_DEFAULT]);
			unset($app[self::TEAM_MIGRATIONS_OPTIONS]);

			return new Pimple($options);
		});

		$app[self::TEAM_MIGRATIONS] = $app->share(function() use ($app) {
			if (isset($app[self::TEAM_MIGRATIONS_PROVIDERS]))
			{
				/** @var AbstractServiceProvider $provider */
				foreach (array_filter($app[self::TEAM_MIGRATIONS_PROVIDERS]) as $provider)
				{
					$provider->register($app);
				}

				unset($app[self::TEAM_MIGRATIONS_PROVIDERS]);
			}

			$config = $app[self::TEAM_MIGRATIONS_CONFIG];
			$classManager = $config[self::OPTION_CLASS_MANAGER];

			/** @var \Moro\Migration\MigrationManager $manager */
			$manager = new $classManager;
			$manager->setContainer($app)
				->setEventDispatcher($app['dispatcher'])
				->setEnvironment($config[self::OPTION_ENVIRONMENT])
				->setValidationKey($config[self::OPTION_VALIDATION_KEY]);

			if (isset($config[self::OPTION_PATH_PROJECT]))
			{
				$manager->setProjectPath($config[self::OPTION_PATH_PROJECT]);
			}
			else
			{
				$config[self::OPTION_PATH_PROJECT] = $manager->getProjectPath();
			}

			return $manager;
		});

		$this->_addListener($app, $app['dispatcher']);
	}

	/**
	 * @param Application $app
	 * @param EventDispatcher $dispatcher
	 */
	protected function _addListener(Application $app, EventDispatcher $dispatcher)
	{
		$dispatcher->addListener(ConsoleEvents::INIT, function(ConsoleEvent $event) use ($app) {
			$event->getApplication()->addCommands([
				new MigrationsStatus  ($app[self::TEAM_MIGRATIONS]),
				new MigrationsCreate  ($app[self::TEAM_MIGRATIONS]),
				new MigrationsMigrate ($app[self::TEAM_MIGRATIONS]),
			]);
		});
	}
}