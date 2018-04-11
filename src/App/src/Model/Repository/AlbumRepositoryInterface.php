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
}
