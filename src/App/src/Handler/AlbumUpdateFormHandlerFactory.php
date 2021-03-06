<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace App\Handler;

use App\Model\InputFilter\AlbumInputFilter;
use App\Model\Repository\AlbumRepositoryInterface;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumUpdateFormHandlerFactory
{
    public function __invoke(ContainerInterface $container) : AlbumUpdateFormHandler
    {
        $template        = $container->get(TemplateRendererInterface::class);
        $albumRepository = $container->get(AlbumRepositoryInterface::class);
        $inputFilter     = $container->get(AlbumInputFilter::class);
        
        return new AlbumUpdateFormHandler(
            $template, $albumRepository, $inputFilter
        );
    }
}
