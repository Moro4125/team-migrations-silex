<?php
/**
 * Bootstrap file for test run "migrations.php" from /vendor/moro/team-migrations-common/bin
 */
namespace bootstrap;
use \Silex\Application;
use \Knp\Provider\ConsoleServiceProvider;
use \Moro\Migration\Provider\TeamMigrationsServiceProvider;
use \Moro\Migration\Provider\Handler\FilesStorageHandlerProvider;

$app = new Application();

$app->register(new ConsoleServiceProvider(), [
	'console.project_directory' => __DIR__,
	'console.name' => 'Bootstrap for test module "moro/team-migrations-silex".',
	'console.version' => '',
]);

$app->register(new TeamMigrationsServiceProvider(), [
	'team.migrations.options' => [
		'environment' => 'development.bootstrap',
	],
	'team.migrations.providers' => [
		new FilesStorageHandlerProvider(),
	]
]);

return $app['console'];