<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\AlbumEntity;

/**
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
interface AlbumRepositoryInterface
{
    /**
     * Fetch all albums.
     * 
     * @return array AlbumEntity[]
     */
    public function fetchAlbumList();

    /**
     * Save an album.
     *
     * Should insert a new album if no identifier is present in the entity, and
     * update an existing album otherwise.
     * 
     * @param AlbumEntity $album
     * @return bool
     */
    public function saveAlbum(AlbumEntity $album);
}
