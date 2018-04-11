<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Storage\StorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Description of AlbumRepositoryFactory
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
class AlbumRepositoryFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AlbumRepository($container->get(StorageInterface::class));
    }
}
