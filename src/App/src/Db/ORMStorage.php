<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace App\Db;

use App\Model\Entity\AlbumEntity;
use App\Model\Storage\StorageInterface;
use Doctrine\ORM\EntityManager;

/**
 * Description of ORMStorage
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
class ORMStorage implements StorageInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAlbumList(): array
    {
        $albums = $this->entityManager
                ->getRepository(AlbumEntity::class)
                ->findAll();

        return $albums;
    }
}
