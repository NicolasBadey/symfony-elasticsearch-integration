<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

exec('bin/console doctrine:database:create --if-not-exists');
exec('bin/console doctrine:schema:update --force');
exec('bin/console hautelook:fixtures:load -n');

require __DIR__.'/../vendor/autoload.php';
