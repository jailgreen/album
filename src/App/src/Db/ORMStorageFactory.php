<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace App\Db;

use Psr\Container\ContainerInterface;

/**
 * Description of ORMStorageFactory
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
class ORMStorageFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new ORMStorage($container->get('doctrine.entity_manager.orm_default'));
    }
}
