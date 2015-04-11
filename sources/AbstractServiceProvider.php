<?php
/**
 * Class AbstractServiceProvider
 */
namespace Moro\Migration\Provider;
use \Silex\ServiceProviderInterface;
use \Silex\Application;

/**
 * Class AbstractServiceProvider
 * @package Moro\Migration\Provider
 */
abstract class AbstractServiceProvider implements ServiceProviderInterface
{
	const TEAM_MIGRATIONS           = 'team-migrations';
	const TEAM_MIGRATIONS_DEFAULT   = 'team-migrations.default';
	const TEAM_MIGRATIONS_OPTIONS   = 'team-migrations.options';
	const TEAM_MIGRATIONS_CONFIG    = 'team-migrations.config';
	const TEAM_MIGRATIONS_PROVIDERS = 'team-migrations.providers';

	/**
	 * @param Application $app
	 */
	public function boot(Application $app)
	{
	}
}