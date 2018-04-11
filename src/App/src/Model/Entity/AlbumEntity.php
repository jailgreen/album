<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity @ORM\Table(name="albums")
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
class AlbumEntity
{
    /**
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $artist;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $title;

    /**
     *
     * @return string
     */
    public function getArtist() : string
    {
        return $this->artist;
    }

    /**
     *
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     *
     * @param string $artist
     * @return void
     */
    public function setArtist(string $artist) : void
    {
        $this->artist = $artist;
    }

    /**
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }

    /**
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }
}
