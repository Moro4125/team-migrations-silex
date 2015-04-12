Team-Migrations-Silex
=====================

Migrations system for storage structure and database schema.

## Requirements
- PHP >= 5.5.16
- [moro/team-migrations-common](https://github.com/Moro4125/team-migrations-common) >= dev-master
- [knplabs/console-service-provider](https://github.com/KnpLabs/ConsoleServiceProvider) ~ 1.0
- [silex/silex](https://github.com/silexphp/Silex) ~ 1.2
- [symfony/console](https://github.com/symfony/Console) ~2.6
- [symfony/event-dispatcher](https://github.com/symfony/EventDispatcher) ~2.0
- [symfony/finder](https://github.com/symfony/Finder) ~2.0

## Installation
    php composer.phar require moro/team-migrations-silex "dev-master"

## Registration
``` php

    <?php // bootstrap.php
    $app->register(new TeamMigrationsServiceProvider(), [
      'team-migrations.options' => [
        'environment' => 'development',
        'path.project' => dirname(__DIR__),
        /* ... */
      ],
      'team-migrations.providers' => [
        new FilesStorageHandlerProvider(),
        /* ... */
      ],
    ]);

```

## License
Package __moro/team-migrations-silex__ is licensed under the MIT license.

2015