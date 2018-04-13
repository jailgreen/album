<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare (strict_types=1);

namespace App\Model\Storage;

use App\Model\Entity\AlbumEntity;

/**
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
interface StorageInterface
{
    /**
     * Fetch a list of albums.
     *
     * @return array AlbumEntity[]
     */
    public function fetchAlbumList();

    /**
     * Insert an album into storage.
     * 
     * @param AlbumEntity $album
     * @return bool
     */
    public function insertAlbum(AlbumEntity $album);

    /**
     * Update an album.
     *
     * @param AlbumEntity $album
     * @return bool
     */
    public function updateAlbum(AlbumEntity $album);

    /**
     * Delete an album.
     *
     * @param AlbumEntity $album
     * @return bool
     */
    public function deleteAlbum(AlbumEntity $album);
}
