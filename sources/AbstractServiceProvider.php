<?php
/**
 * Class AbstractServiceProvider
 */
namespace Moro\Migration\Provider;

/**
 * Class AbstractServiceProvider
 * @package Moro\Migration\Provider
 */
abstract class AbstractServiceProvider
{
	const TEAM_MIGRATIONS           = 'team-migrations';
	const TEAM_MIGRATIONS_DEFAULT   = 'team-migrations.default';
	const TEAM_MIGRATIONS_OPTIONS   = 'team-migrations.options';
	const TEAM_MIGRATIONS_CONFIG    = 'team-migrations.config';
	const TEAM_MIGRATIONS_PROVIDERS = 'team-migrations.providers';
}