Team-Migrations-Silex
=====================

Migrations system for storage structure and database schema.

## Requirements
- PHP >= 5.5
- [moro/team-migrations-common](https://github.com/Moro4125/team-migrations-common) ~ 1.6.1
- [silex/silex](https://github.com/silexphp/Silex) ~ 2.0
- [symfony/console](https://github.com/symfony/Console) ~3.0
- [symfony/event-dispatcher](https://github.com/symfony/EventDispatcher) ~3.0
- [symfony/finder](https://github.com/symfony/Finder) ~3.0

## Installation
    php composer.phar require moro/team-migrations-silex "~2.0"

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

2015-2016