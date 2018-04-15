<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\AlbumEntity;
use App\Model\Storage\StorageInterface;

/**
 * Description of AlbumRepository
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
class AlbumRepository implements AlbumRepositoryInterface
{
    /**
     *
     * @var StorageInterface
     */
    private $albumStorage;

    /**
     * AlbumRepository constructor.
     *
     * @param StorageInterface $albumStorage
     */
    public function __construct(StorageInterface $albumStorage)
    {
        $this->albumStorage = $albumStorage;
    }

    /**
     * 
     * {@inheritDoc}
     */
    public function fetchAlbumList(): array
    {
        return $this->albumStorage->fetchAlbumList();
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAlbum(int $id)
    {
        return $this->albumStorage->fetchAlbumById($id);
    }

    /**
     * {@inheritDoc}
     */
    public function saveAlbum(AlbumEntity $album): bool
    {
        if (null === $album->getId()) {
            return $this->albumStorage->insertAlbum($album);
        }

        return $this->albumStorage->updateAlbum($album);
    }

    public function deleteAlbum(AlbumEntity $album): bool
    {
        return $this->albumStorage->deleteAlbum($album);
    }
}
