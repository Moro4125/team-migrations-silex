<?php
/**
 * Class TeamMigrationsServiceProvider
 */
namespace Moro\Migration\Provider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;
use Silex\Api\BootableProviderInterface;
use Moro\Migration\MigrationManager;
use Moro\Migration\Command\MigrationsCreate;
use Moro\Migration\Command\MigrationsMigrate;
use Moro\Migration\Command\MigrationsStatus;

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
class TeamMigrationsServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
	const VERSION = '2.1.1';

	const OPTION_ENVIRONMENT    = 'environment';
	const OPTION_CLASS_MANAGER  = 'class.manager';
	const OPTION_VALIDATION_KEY = 'validation.key';
	const OPTION_PATH_PROJECT   = 'path.project';

	/**
	 * @param Container $app
	 */
	public function register(Container $app)
	{
		$app[self::TEAM_MIGRATIONS_OPTIONS] = [];

		$app[self::TEAM_MIGRATIONS_DEFAULT] = [
			self::OPTION_ENVIRONMENT    => getenv('ENVIRONMENT') ?: 'production',
			self::OPTION_CLASS_MANAGER  => MigrationManager::class,
			self::OPTION_VALIDATION_KEY => '',
		];

		$app[self::TEAM_MIGRATIONS_CONFIG] = function() use ($app) {
			$options = array_merge($app[self::TEAM_MIGRATIONS_DEFAULT], $app[self::TEAM_MIGRATIONS_OPTIONS]);

			unset($app[self::TEAM_MIGRATIONS_DEFAULT]);
			unset($app[self::TEAM_MIGRATIONS_OPTIONS]);

			return new Container($options);
		};

		$app[self::TEAM_MIGRATIONS] = function() use ($app) {
			if (isset($app[self::TEAM_MIGRATIONS_PROVIDERS]))
			{
				/** @var ServiceProviderInterface $provider */
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
		};
	}

	/**
	 * Bootstraps the application.
	 *
	 * This method is called after all services are registered
	 * and should be used for "dynamic" configuration (whenever
	 * a service must be requested).
	 *
	 * @param Application $app
	 */
	public function boot(Application $app)
	{
		/** @var \Symfony\Component\Console\Application $console */
		if (php_sapi_name() === 'cli' && isset($app['console']) && $console = $app['console'])
		{
			$console->addCommands([
				new MigrationsStatus  ($app[self::TEAM_MIGRATIONS]),
				new MigrationsCreate  ($app[self::TEAM_MIGRATIONS]),
				new MigrationsMigrate ($app[self::TEAM_MIGRATIONS]),
			]);
		}
	}
}