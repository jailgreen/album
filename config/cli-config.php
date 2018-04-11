<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

$container = require 'container.php';

$entityManager = $container->get('doctrine.entity_manager.orm_default');
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
/**
return new \Symfony\Component\Console\Helper\HelperSet([
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper(
        $container->get('doctrine.entity_manager.orm_default')
    ),
]);
 *
 */
