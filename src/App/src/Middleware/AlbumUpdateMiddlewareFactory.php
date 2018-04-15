<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Model\InputFilter\AlbumInputFilter;
use App\Model\Repository\AlbumRepositoryInterface;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;

class AlbumUpdateMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : AlbumUpdateMiddleware
    {
        $router          = $container->get(RouterInterface::class);
        $albumRepository = $container->get(AlbumRepositoryInterface::class);
        $inputFilter     = $container->get(AlbumInputFilter::class);
        
        return new AlbumUpdateMiddleware($router, $albumRepository, $inputFilter);
    }
}
