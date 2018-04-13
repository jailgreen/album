<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace App\Model\InputFilter;

use Psr\Container\ContainerInterface;

/**
 * Description of AlbumInputFilterFactory
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
class AlbumInputFilterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $inputFilter = new AlbumInputFilter();
        $inputFilter->init();

        return $inputFilter;
    }
}
